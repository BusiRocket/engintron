#!/usr/local/cpanel/3rdparty/bin/php
<?php

/**
 * @version    2.4
 * @package    Engintron for cPanel/WHM
 * @author     Fotis Evangelou (https://kodeka.io)
 * @url        https://engintron.com
 * @copyright  Copyright (c) 2014 - 2024 Kodeka OÜ. All rights reserved.
 * @license    GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 */

if (file_exists('/etc/apache2/conf/httpd.conf') && is_readable('/etc/apache2/conf/httpd.conf')) {
    define('HTTPD_CONF', '/etc/apache2/conf/httpd.conf');
} elseif (file_exists('/etc/httpd/conf/httpd.conf') && is_readable('/etc/httpd/conf/httpd.conf')) {
    define('HTTPD_CONF', '/etc/httpd/conf/httpd.conf');
} else {
    define('HTTPD_CONF', '/usr/local/apache/conf/httpd.conf');
}
define('HTTPD_CONF_LAST_CHANGED', 15); /* In seconds */
define('HTTPD_HTTPS_PORT', '8443');
define('NGINX_HTTPS_PORT', '443');
define('NGINX_DEFAULT_HTTPS_VHOST', '/etc/nginx/conf.d/default_https.conf');

$osDistro = '';
$osRelease = '';
if (file_exists('/etc/redhat-release') && is_readable('/etc/redhat-release')) {
    $osDistro = 'el';
    $osRelease = shell_exec('rpm -q --qf %{version} `rpm -q --whatprovides redhat-release` | cut -c 1');
} else {
    $osDistro = 'ubuntu';
    $osRelease = shell_exec('lsb_release -r -s');
}
define('DISTRO', $osDistro);
define('RELEASE', $osRelease);
$nginx_version_output = shell_exec('nginx -V 2>&1');
define('NGINX_VERSION', trim(str_replace('nginx version: nginx/', '', preg_match('/nginx\/(\d+\.\d+\.\d+)/', $nginx_version_output, $matches) ? $matches[1] : '')));
define('NGINX_HAS_HTTP3', strpos($nginx_version_output, '--with-http_v3_module') !== false);

function generate_https_vhosts()
{
    $hostnamePemFile = '';
    if (file_exists('/var/cpanel/ssl/cpanel/cpanel.pem') && is_readable('/var/cpanel/ssl/cpanel/cpanel.pem')) {
        $hostnamePemFile = '/var/cpanel/ssl/cpanel/cpanel.pem';
    }
    if (file_exists('/var/cpanel/ssl/cpanel/mycpanel.pem') && is_readable('/var/cpanel/ssl/cpanel/mycpanel.pem')) {
        $hostnamePemFile = '/var/cpanel/ssl/cpanel/mycpanel.pem';
    }

    // Handle http2 and http3 placement
    $http2_on_listen = ' http2';
    $http2_standalone = '';
    if (version_compare(NGINX_VERSION, '1.25.1', '>=')) {
        $http2_on_listen = '';
        $http2_standalone = 'http2 on;';
    }

    $http3_directives = '
    set $HTTP3_AVAILABLE false;';
    $http3_directives_host = '
    set $HTTP3_AVAILABLE false;';
    if (NGINX_HAS_HTTP3 && version_compare(NGINX_VERSION, '1.25.0', '>=')) {
        $http3_directives = '
    listen ' . NGINX_HTTPS_PORT . ' quic reuseport default_server;
    listen [::]:' . NGINX_HTTPS_PORT . ' quic reuseport default_server;
    
    # Add Alt-Svc header to negotiate HTTP/3.
    add_header Alt-Svc \'h3=":' . NGINX_HTTPS_PORT . '"; ma=86400\';
    set $HTTP3_AVAILABLE true;';

        $http3_directives_host = '
    listen ' . NGINX_HTTPS_PORT . ' quic;
    listen [::]:' . NGINX_HTTPS_PORT . ' quic;
    
    # Add Alt-Svc header to negotiate HTTP/3.
    add_header Alt-Svc \'h3=":' . NGINX_HTTPS_PORT . '"; ma=86400\';
    set $HTTP3_AVAILABLE true;';
    }

    // Initialize the output for default_https.conf
    $output = '
# /**
#  * @version    2.4
#  * @package    Engintron for cPanel/WHM
#  * @author     Fotis Evangelou (https://kodeka.io)
#  * @url        https://engintron.com
#  * @copyright  Copyright (c) 2014 - 2024 Kodeka OÜ. All rights reserved.
#  * @license    GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
#  */

# Default definition block for HTTPS (Generated on ' . @date('Y.m.d H:i:s') . ') #
server {
    ' . $http3_directives . '
    listen ' . NGINX_HTTPS_PORT . ' ssl' . $http2_on_listen . ' default_server;
    listen [::]:' . NGINX_HTTPS_PORT . ' ssl' . $http2_on_listen . ' default_server ipv6only=off;
    ' . $http2_standalone . '
    server_name localhost;

    # deny all; # DO NOT REMOVE OR CHANGE THIS LINE - Used when Engintron is disabled to block Nginx from becoming an open proxy
    
    set $HTTPS_PORT ' . NGINX_HTTPS_PORT . ';

    ssl_certificate ' . $hostnamePemFile . ';
    ssl_certificate_key ' . $hostnamePemFile . ';

    # OCSP Stapling
    #ssl_trusted_certificate ' . $hostnamePemFile . ';
    #ssl_stapling on;
    #ssl_stapling_verify on;

    include common_https.conf;

    location = /nginx_status {
        stub_status;
        access_log off;
        log_not_found off;
        # Uncomment the following 2 lines to make the Nginx status page private.
        # If you do this and you have Munin installed, graphs for Nginx will stop working.
        #allow 127.0.0.1;
        #deny all;
    }

    location = /whm-server-status {
        proxy_pass http://127.0.0.1:8080;
        # Comment the following 2 lines to make the Apache status page public
        allow 127.0.0.1;
        deny all;
    }
}
    ';

    // Process Apache vhosts
    if (file_exists(HTTPD_CONF) && is_readable(HTTPD_CONF)) {
        $file = file_get_contents(HTTPD_CONF);
        $regex = "#\<VirtualHost [0-9a-f\.\:\[\]\s]+\:" . HTTPD_HTTPS_PORT . "\>(.+?)\<\/VirtualHost\>#s";
        preg_match_all($regex, $file, $matches, PREG_PATTERN_ORDER);
        $vhosts = $matches[1];
        if (count($vhosts)) {
            foreach ($vhosts as $vhost) {
                if ($hostnamePemFile && strpos($vhost, $hostnamePemFile) !== false) {
                    continue;
                } // Skip the main hostname entry
                preg_match("#ServerName (.+?)\n#s", $vhost, $name);
                preg_match_all("#ServerAlias (.+?)\n#s", $vhost, $aliases);
                preg_match("#SSLCertificateFile (.+?)(\n|\r)#s", $vhost, $certfile);
                preg_match("#SSLCertificateKeyFile (.+?)(\n|\r)#s", $vhost, $certkeyfile);
                preg_match("#SSLCACertificateFile (.+?)(\n|\r)#s", $vhost, $certcafile);
                if ($aliases[1]) {
                    $vhostAliases = implode(' ', $aliases[1]);
                } else {
                    $vhostAliases = '';
                }
                $vhostDomains = trim($name[1] . ' ' . $vhostAliases);
                $vhostDomainsForNginx = explode(' ', $vhostDomains);
                $vhostDomainsForNginx = implode(PHP_EOL . '        ', $vhostDomainsForNginx);
                $vhostDomainsAsComment = str_split($vhostDomains, 250);
                $vhostDomainsAsComment = implode(PHP_EOL . '# ', $vhostDomainsAsComment);
                $vhostCertFile = $certfile[1];
                $vhostCertKeyFile = $certkeyfile[1];
                if (strpos($vhostCertFile, '/combined') !== false) {
                    $fullChainCertName = $vhostCertFile;
                    $vhostCertKeyFile = $vhostCertFile;
                } else {
                    $fullChainCertName = str_replace('/var/cpanel/ssl/installed/certs/', '/etc/ssl/engintron/', $vhostCertFile);
                    if ($certcafile[1]) {
                        $vhostCertCAFile = $certcafile[1];
                        $vhostFullChainCert = file_get_contents($vhostCertFile) . "\n" . file_get_contents($vhostCertCAFile);
                        $ocspStapling = '
    # OCSP Stapling
    #ssl_trusted_certificate ' . $fullChainCertName . ';
    #ssl_stapling on;
    #ssl_stapling_verify on;
                    ';
                    } else {
                        $vhostFullChainCert = file_get_contents($vhostCertFile);
                        $ocspStapling = '';
                    }
                    file_put_contents($fullChainCertName, $vhostFullChainCert);
                }

                $output .= '
# Definition block for domain(s): ' . $vhostDomainsAsComment . ' #
server {
    ' . $http3_directives_host . '
    listen ' . NGINX_HTTPS_PORT . ' ssl' . $http2_on_listen . ';
    listen [::]:' . NGINX_HTTPS_PORT . ' ssl' . $http2_on_listen . ';
    ' . $http2_standalone . '
    server_name ' . $vhostDomainsForNginx . ';
    # deny all; # DO NOT REMOVE OR CHANGE THIS LINE - Used when Engintron is disabled to block Nginx from becoming an open proxy
    set $HTTPS_PORT ' . NGINX_HTTPS_PORT . ';
    ssl_certificate ' . $fullChainCertName . ';
    ssl_certificate_key ' . $vhostCertKeyFile . ';
    ' . $ocspStapling . '
    include common_https.conf;
}
                ';
            }
        }
    }
    file_put_contents(NGINX_DEFAULT_HTTPS_VHOST, $output);
}

// Run the check
if (!file_exists(NGINX_DEFAULT_HTTPS_VHOST) || (file_exists(HTTPD_CONF) && is_readable(HTTPD_CONF) && (filemtime(HTTPD_CONF) + HTTPD_CONF_LAST_CHANGED) > time())) {
    generate_https_vhosts();
    exit(1);
}

exit(0);
