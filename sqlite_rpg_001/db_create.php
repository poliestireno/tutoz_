<?php
//###############################################################################
// Name:        CloudManager
// Description: A simple cloud based game load and save system for RPG Maker MV
// Version:     0.9.0 (First release)
// Author:      Frank A. Grenzel
// License:     CC BY 3.0
//###############################################################################
//
// ### Terms of Use ###
// The Cloud Manager is distributed as is under the creative commons license CC BY 3.0
// (Attribution 3.0 Unported) for free.
// You are free to share, copy, redistribute or edit it for any purpose, even commercially
// under the following terms: You must give appropriate credit, provide a link to the
// license, and indicate if changes were made. You may do so in any reasonable manner,
// but not in any way that suggests the licensor endorses you or your use.
// 
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
// INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A 
// PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR
// ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
// OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
// OTHER DEALINGS IN THE SOFTWARE.

	try {

//phpinfo();

		$db = new PDO('sqlite:rmmv.sqlite');
		// Throw exceptions on error
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$db->exec("CREATE TABLE IF NOT EXISTS savegames(
			id INTEGER PRIMARY KEY AUTOINCREMENT, 
			data TEXT NOT NULL DEFAULT '',
			gameName VARCHAR(255) NOT NULL DEFAULT '',
			gameVersion FLOAT NOT NULL DEFAULT 0,
			userName VARCHAR(255) NOT NULL DEFAULT '',
			timestamp DATETIME DEFAULT CURRENT_TIMESTAMP)");
		
		$db->exec("CREATE TABLE IF NOT EXISTS sessiondata(
			id INTEGER PRIMARY KEY AUTOINCREMENT, 
			guid VARCHAR(255) NOT NULL DEFAULT '',
			action VARCHAR(255) NOT NULL DEFAULT '',
			gameName VARCHAR(255) NOT NULL DEFAULT '',
			gameVersion FLOAT NOT NULL DEFAULT 0,
			userName VARCHAR(255) NOT NULL DEFAULT '',
			timestamp DATETIME DEFAULT CURRENT_TIMESTAMP)");		
		
		$db->exec("CREATE TABLE IF NOT EXISTS sessions(
			id INTEGER PRIMARY KEY AUTOINCREMENT,
			guid VARCHAR(255) NOT NULL DEFAULT '',
			action VARCHAR(255) NOT NULL DEFAULT '',
			gameName VARCHAR(255) NOT NULL DEFAULT '',
			gameVersion FLOAT NOT NULL DEFAULT 0,
			userName VARCHAR(255) NOT NULL DEFAULT '',
			timestamp DATETIME DEFAULT CURRENT_TIMESTAMP)");	

		$db = NULL;
	
	} catch(PDOException $e) {
		print 'Exception : '.$e->getMessage();
	}
?>