#!/usr/bin/perl
#
# Peteris Krumins (peter@catonmat.net)
# http://www.catonmat.net  --  good coders code, great reuse
#
# A simple TCP proxy that implements IP-based access control
# It proxies data from Growatt ShineLink to the Growatt servers
# and stores relevant data in a temporary file.
#
# Written for the article "Turn any Linux computer into SOCKS5
# proxy in one command," which can be read here:
#
# http://www.catonmat.net/blog/linux-socks5-proxy
#

use warnings;
use strict;

use IO::Socket;
use IO::Select;

use Data::Hexify;


my @allowed_ips = ('1.2.3.4', '5.6.7.8', '127.0.0.1', '192.168.0.1');
my $ioset = IO::Select->new;
my %socket_map;

my $debug = 1;

sub new_conn {
    my ($host, $port) = @_;
    return IO::Socket::INET->new(
        PeerAddr => $host,
        PeerPort => $port
    ) || die "Unable to connect to $host:$port: $!";
}

sub new_server {
    my ($host, $port) = @_;
    my $server = IO::Socket::INET->new(
        LocalAddr => $host,
        LocalPort => $port,
        ReuseAddr => 1,
        Listen    => 100
    ) || die "Unable to listen on $host:$port: $!";
}

sub new_connection {
    my $server = shift;
    my $client = $server->accept;
    my $client_ip = client_ip($client);

    unless (client_allowed($client)) {
        print "Connection from $client_ip denied.\n" if $debug;
        $client->close;
        return;
    }
    print "Connection from $client_ip accepted.\n" if $debug;

    my $remote = new_conn('server.growatt.com', 5279);
    $ioset->add($client);
    $ioset->add($remote);

    $socket_map{$client} = $remote;
    $socket_map{$remote} = $client;
}

sub close_connection {
    my $client = shift;
    my $client_ip = client_ip($client);
    my $remote = $socket_map{$client};
    
    $ioset->remove($client);
    $ioset->remove($remote);

    delete $socket_map{$client};
    delete $socket_map{$remote};

    $client->close;
    $remote->close;

    print "Connection from $client_ip closed.\n" if $debug;
}

sub client_ip {
    my $client = shift;
    return inet_ntoa($client->sockaddr);
}

sub client_allowed {
    my $client = shift;
    my $client_ip = client_ip($client);
    return grep { $_ eq $client_ip } @allowed_ips;
}

sub save_to_file {
	my $msg = shift;
	my $filename = 'report.txt';
	print ("Write", Hexify($msg), "\n");
	open(my $fh, '>', $filename) or die "Could not open file '$filename' $!";
	print $fh $msg;
	close $fh;
}

sub save_to_file2 {
	my $msg = shift;
	my $filename = 'report13.txt';
	print ("Write", Hexify($msg), "\n");
	open(my $fh, '>', $filename) or die "Could not open file '$filename' $!";
	print $fh $msg;
	close $fh;
}

print "Starting a server on 0.0.0.0:5279\n";
my $server = new_server('0.0.0.0', 5279);
$ioset->add($server);

while (1) {
    for my $socket ($ioset->can_read) {
        if ($socket == $server) {
            new_connection($server);
        }
        else {
            next unless exists $socket_map{$socket};
            my $remote = $socket_map{$socket};
            my $buffer;
            my $read = $socket->sysread($buffer, 4096);
            if ($read) {
                $remote->syswrite($buffer);
		print ("Read" , Hexify(\$buffer), "\n");
		if (substr($buffer, 6, 2) eq "\x51\x04" && length($buffer) > 100) {
			save_to_file($buffer);
		}
		if (substr($buffer, 6, 2) eq "\x51\x03" && length($buffer) > 100) {
			save_to_file2($buffer);
		}
            }
            else {
                close_connection($socket);
            }
        }
    }
}
