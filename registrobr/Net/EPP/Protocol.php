<?php

/*####EPP Client class for PHP, Copyright 2005 CentralNic Ltd
####This program is free software; you can redistribute it and/or modify
####it under the terms of the GNU General Public License as published by
####the Free Software Foundation; either version 2 of the License, or
####(at your option) any later version.

####This program is distributed in the hope that it will be useful,
####but WITHOUT ANY WARRANTY; without even the implied warranty of
####MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
####GNU General Public License for more details.

####You should have received a copy of the GNU General Public License
####along with this program; if not, write to the Free Software
####Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/**
* Low-level functions useful for both EPP clients and servers
* @package Net_EPP
* @version 0.0.4
* @author Gavin Brown <gavin.brown@nospam.centralnic.com>
* @revision $Id: Protocol.php,v 1.4 2011/06/28 09:48:02 gavin Exp $
*/

require_once('PEAR.php');

/**
* Low-level functions useful for both EPP clients and servers
* @package Net_EPP
*/
class Net_EPP_Protocol {

####static function _fread_nb($socket,$length) {
########$result = '';

########// Loop reading and checking info to see if we hit timeout
########$info = stream_get_meta_data($socket);
########while (!$info['timed_out'] && !feof($socket)) {
############// Try read remaining data from socket
############$buffer = @fread($socket,$length - strlen($result));
############// If the buffer actually contains something then add it to the result
############if ($buffer !== false) {
################$result .= $buffer;
################// If we hit the length we looking for, break
################if (strlen($result) == $length) {
####################break;
################}
############} else {
################// Sleep 0.25s
####       #### ########usleep(2500);
############}
############// Update metadata
############$info = stream_get_meta_data($socket);
########}

########// Check for timeout
########if ($info['timed_out']) {
############return new PEAR_Error('Timeout while reading data from socket');
########}

########return $result;
####}

####/**
####* get an EPP frame from the remote peer
####* @param resource $socket a socket connected to the remote peer
####* @return PEAR_Error|string either an error or a string
####*/
####static function getFrame($socket) {
########// Read header
########if (PEAR::isError($hdr = Net_EPP_Protocol::_fread_nb($socket,4))) {
####       ########return $hdr;
########}

########// Unpack first 4 bytes which is our length
########$unpacked = unpack('N', $hdr);
########$length = $unpacked[1];
########if ($length < 5) {
############return new PEAR_Error(sprintf('Got a bad frame header length of %d bytes from peer', $length));

########} else {
############$length -= 4; // discard the length of the header itself
############// Read frame
############return Net_EPP_Protocol::_fread_nb($socket,$length);
########}
####}

####/**
####* send an EPP frame to the remote peer
####* @param resource $socket a socket connected to the remote peer
####* @param string $xml the XML to send
####*/
####static function sendFrame($socket, $xml) {
########// Grab XML length & add on 4 bytes for the counter
########$length = strlen($xml) + 4;
########$res = fwrite($socket, pack('N',$length) . $xml);
########// Check our write matches
########if ($length != $res) {
############return new PEAR_Error("Short write when sending XML");
########}

########return $res;
####}
}
