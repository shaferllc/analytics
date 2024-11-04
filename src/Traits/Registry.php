<?php

namespace Shaferllc\Analytics\Traits;

use DateTime;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Shaferllc\Analytics\Services\RegistryService;

trait Registry
{
    private const WHOIS_SERVERS = [
        'com' => 'whois.verisign-grs.com',
        'net' => 'whois.verisign-grs.com',
        'org' => 'whois.pir.org',
        'info' => 'whois.afilias.net',
        'biz' => 'whois.biz',
        'io' => 'whois.nic.io',
        'uk' => 'whois.nic.uk',
        'co.uk' => 'whois.nic.uk',
        'ca' => 'whois.cira.ca',
        'au' => 'whois.auda.org.au',
        'de' => 'whois.denic.de',
        'fr' => 'whois.nic.fr',
        'nl' => 'whois.domain-registry.nl',
        'ru' => 'whois.tcinet.ru',
        'eu' => 'whois.eu',
        'me' => 'whois.nic.me',
        'us' => 'whois.nic.us',
        'co' => 'whois.nic.co',
        'app' => 'whois.nic.google',
        'dev' => 'whois.nic.google',
        'ai' => 'whois.nic.ai',
        'cloud' => 'whois.nic.cloud',
        'shop' => 'whois.nic.shop',
        'tech' => 'whois.nic.tech',
        'xyz' => 'whois.nic.xyz',
        'site' => 'whois.nic.site',
        'online' => 'whois.nic.online',
        'store' => 'whois.nic.store'
    ];

    private function extractStatusDescription(array $statuses): array 
    {
        return array_combine($statuses, array_map([$this, 'getStatusCodeDescription'], $statuses));
    }

    private function getRegistrationHistory(string $domain): array
    {
        try {
            $whoisData = $this->queryWhoisServer($this->getWhoisServer($domain), $domain);
            $history = [];
            
            if (!empty($whoisData)) {
                $history[] = [
                    'date' => $this->parseDate($this->extractValue($whoisData, ['Creation Date:', 'created:'])),
                    'registrar' => $this->extractValue($whoisData, ['Registrar:', 'registrar:']),
                    'status' => $this->extractValue($whoisData, ['Status:', 'status:'])
                ];
            }

            return $history;
        } catch (\Exception $e) {
            Log::error("Failed to get registration history: " . $e->getMessage());
            return [];
        }
    }

    private function getNameserverHistory(string $domain): array 
    {
        try {
            $records = dns_get_record($domain, DNS_NS);
            $history = [];
            
            if (!empty($records)) {
                $history[] = [
                    'date' => date('Y-m-d H:i:s'),
                    'nameservers' => array_column($records, 'target')
                ];
            }

            return $history;
        } catch (\Exception $e) {
            Log::error("Failed to get nameserver history: " . $e->getMessage());
            return [];
        }
    }

    private function getIPHistory(string $domain): array
    {
        try {
            $records = dns_get_record($domain, DNS_A);
            $history = [];
            
            if (!empty($records)) {
                $history[] = [
                    'date' => date('Y-m-d H:i:s'),
                    'ip' => array_column($records, 'ip')
                ];
            }

            return $history;
        } catch (\Exception $e) {
            Log::error("Failed to get IP history: " . $e->getMessage());
            return [];
        }
    }

    private function findSimilarDomains(string $domain): array
    {
        try {
            $baseDomain = preg_replace('/\.[^.]+$/', '', $domain);
            $tld = $this->getTLD($domain);
            
            $similar = [];
            foreach (self::WHOIS_SERVERS as $extension => $server) {
                $testDomain = $baseDomain . '.' . $extension;
                if (@dns_get_record($testDomain, DNS_A)) {
                    $similar[] = $testDomain;
                }
            }

            return $similar;
        } catch (\Exception $e) {
            Log::error("Failed to find similar domains: " . $e->getMessage());
            return [];
        }
    }

    private function findRelatedDomains(string $domain): array
    {
        try {
            $ip = gethostbyname($domain);
            $related = [];
            
            if ($ip && filter_var($ip, FILTER_VALIDATE_IP)) {
                $hostDomains = gethostbynamel($ip);
                if ($hostDomains) {
                    $related = array_diff($hostDomains, [$domain]);
                }
            }

            return $related;
        } catch (\Exception $e) {
            Log::error("Failed to find related domains: " . $e->getMessage());
            return [];
        }
    }

    private function getDomainReputation(string $domain): ?array
    {
        try {
            $records = [
                'has_dns' => (bool)dns_get_record($domain),
                'has_www' => (bool)dns_get_record('www.' . $domain),
                'has_mx' => (bool)dns_get_record($domain, DNS_MX),
                'has_spf' => (bool)dns_get_record($domain, DNS_TXT)
            ];
            
            return $records;
        } catch (\Exception $e) {
            Log::error("Failed to get domain reputation: " . $e->getMessage());
            return null;
        }
    }

    private function getSSLInformation(string $domain): ?array
    {
        try {
            $context = stream_context_create(['ssl' => ['capture_peer_cert' => true]]);
            $socket = @stream_socket_client("ssl://{$domain}:443", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $context);
            
            if ($socket) {
                $cert = stream_context_get_params($socket);
                $certInfo = openssl_x509_parse($cert['options']['ssl']['peer_certificate']);
                fclose($socket);
                return $certInfo;
            }

            return null;
        } catch (\Exception $e) {
            Log::error("Failed to get SSL information: " . $e->getMessage());
            return null;
        }
    }

    private function getDNSRecords(string $domain): array
    {
        try {
            $records = [];
            foreach (['A', 'AAAA', 'MX', 'NS', 'TXT', 'SOA', 'CNAME'] as $type) {
                $result = dns_get_record($domain, constant("DNS_{$type}"));
                if ($result) {
                    $records[$type] = $result;
                }
            }
            
            return $records;
        } catch (\Exception $e) {
            Log::error("Failed to get DNS records: " . $e->getMessage());
            return [];
        }
    }

    private function getTrademarkInfo(string $domain): ?array
    {
        // No free trademark APIs available
        return null;
    }

    private function getStatusCodeDescription(string $status): string
    {
        $descriptions = [
            'clientTransferProhibited' => 'Domain cannot be transferred to another registrar',
            'clientUpdateProhibited' => 'Domain cannot be updated',
            'clientDeleteProhibited' => 'Domain cannot be deleted',
            'serverTransferProhibited' => 'Registry-level transfer prohibition',
            'serverUpdateProhibited' => 'Registry-level update prohibition',
            'serverDeleteProhibited' => 'Registry-level delete prohibition',
            'ok' => 'Domain is active',
            'inactive' => 'Domain is not active',
            'pendingDelete' => 'Domain is pending deletion',
            'pendingTransfer' => 'Domain is pending transfer',
            'pendingUpdate' => 'Domain is pending update',
            'pendingCreate' => 'Domain is pending creation'
        ];
        
        return $descriptions[$status] ?? 'Unknown status code';
    }

    private function extractValue(string $data, array $patterns): ?string
    {
        $lines = explode("\n", $data);
        
        foreach ($lines as $line) {
            foreach ($patterns as $pattern) {
                if (str_contains($line, $pattern)) {
                    $value = trim(str_replace($pattern, '', $line));
                    $value = preg_replace('/\s+/', ' ', $value);
                    $value = trim($value, ": \t\n\r\0\x0B");
                    
                    if (!empty($value)) {
                        return $value;
                    }
                }
            }
        }
        
        return null;
    }

    private function getTLD(string $domain): string
    {
        $domain = preg_replace('#^https?://(www\.)?#', '', $domain);
        $parts = explode('.', $domain);
        
        if (count($parts) >= 3) {
            $lastTwo = implode('.', array_slice($parts, -2));
            if (isset(self::WHOIS_SERVERS[$lastTwo])) {
                return $lastTwo;
            }
        }
        
        return end($parts);
    }

    private function queryWhoisServer(string $server, string $domain): string
    {
        try {
            $socket = @fsockopen($server, 43, $errno, $errstr, 10);
            
            if ($socket) {
                fwrite($socket, $domain . "\r\n");
                
                $response = '';
                while (!feof($socket)) {
                    $response .= fgets($socket, 128);
                }
                
                fclose($socket);
                
                $response = trim($response);
                return preg_replace('/\r\n?/', "\n", $response);
            }

            throw new \Exception("Failed to connect to WHOIS server: $errstr ($errno)");
        } catch (\Exception $e) {
            Log::error("WHOIS query failed for domain {$domain}: " . $e->getMessage());
            return '';
        }
    }

    private function parseDate(?string $dateString): ?string
    {
        if (empty($dateString)) {
            return null;
        }

        try {
            $dateString = preg_replace('/\([^)]+\)/', '', $dateString);
            
            $formats = [
                'Y-m-d\TH:i:s\Z',
                'Y-m-d H:i:s',
                'Y-m-d',
                'd-M-Y',
                'Y.m.d',
                'Y/m/d',
                'Y. m. d.',
                'd.m.Y',
                'd/m/Y',
                'M d Y',
                'd M Y'
            ];

            foreach ($formats as $format) {
                $date = DateTime::createFromFormat($format, trim($dateString));
                if ($date !== false) {
                    return $date->format('Y-m-d H:i:s');
                }
            }

            $timestamp = strtotime($dateString);
            if ($timestamp !== false) {
                return date('Y-m-d H:i:s', $timestamp);
            }

            return null;

        } catch (\Exception $e) {
            Log::error("Date parsing failed: " . $e->getMessage());
            return null;
        }
    }

    private function extractMultipleValues(string $text, string $pattern): array
    {
        preg_match_all($pattern, $text, $matches);
        return $matches[1] ?? [];
    }

    private function getDomainAnalytics(string $domain): array
    {
        // No free domain analytics APIs available
        return [];
    }

    private function getSecurityInfo(string $domain): array
    {
        try {
            $info = [];
            
            // Check basic security headers
            $headers = get_headers("https://{$domain}", 1);
            
            $info['security_headers'] = [
                'has_hsts' => isset($headers['Strict-Transport-Security']),
                'has_xframe' => isset($headers['X-Frame-Options']),
                'has_xss_protection' => isset($headers['X-XSS-Protection']),
                'has_content_security' => isset($headers['Content-Security-Policy'])
            ];
            
            return $info;
        } catch (\Exception $e) {
            Log::error("Failed to get security info: " . $e->getMessage());
            return [];
        }
    }

    private function getHostingInfo(string $domain): array
    {
        try {
            $info = [];
            
            // Get IP address
            $ip = gethostbyname($domain);
            if ($ip && filter_var($ip, FILTER_VALIDATE_IP)) {
                $info['ip'] = $ip;
                
                // Try to get hosting info from IP
                $hostInfo = gethostbyaddr($ip);
                if ($hostInfo && $hostInfo !== $ip) {
                    $info['host'] = $hostInfo;
                }
            }
            
            return $info;
        } catch (\Exception $e) {
            Log::error("Failed to get hosting info: " . $e->getMessage());
            return [];
        }
    }
    private function getWhoisServer(string $domain): string
    {
        try {
            $parts = explode('.', $domain);
            $tld = strtolower(end($parts));
            
            // Check for special cases like co.uk
            if (count($parts) > 2) {
                $secondLevel = strtolower($parts[count($parts)-2]);
                $combinedTld = $secondLevel . '.' . $tld;
                if (isset(self::WHOIS_SERVERS[$combinedTld])) {
                    return self::WHOIS_SERVERS[$combinedTld];
                }
            }
            
            if (isset(self::WHOIS_SERVERS[$tld])) {
                return self::WHOIS_SERVERS[$tld];
            }
            
            throw new \Exception("No WHOIS server found for TLD: {$tld}");
        } catch (\Exception $e) {
            Log::error("Failed to get WHOIS server: " . $e->getMessage());
            throw $e;
        }
    }
}