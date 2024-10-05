<?php
declare(strict_types=1);

use Pardusmapper\Core\ApiResponse;
use Pardusmapper\Core\MySqlDB;
use Pardusmapper\CORS;
use Pardusmapper\DB;
use Pardusmapper\Request;

require_once('../app/settings.php');

CORS::pardus();

// REVIEW
// i don't think this is related to flying close
// all those checks for Critter, Xmas...
// the map array elements only contail two items instead of 3
// disable this endpoint
http_response(true, ApiResponse::NOTIMPLEMENTED, sprintf('feature not implemented'));

debug($_REQUEST);

$mapdata = Request::pstring(key: 'mapdata');
http_response(is_null($mapdata), ApiResponse::BADREQUEST, sprintf('mapdata query parameter is required or invalid: %s', $mapdata ?? 'null'));


// Set Univers Variable and Session Name
$uni = Request::uni();
http_response(is_null($uni), ApiResponse::BADREQUEST, sprintf('uni query parameter is required or invalid: %s', $uni ?? 'null'));

// Get Version
$minVersion = 5.8;
$version = Request::version();
http_response($version < $minVersion, ApiResponse::BADREQUEST, sprintf('version query parameter is required or invalid: %s ... minumum version: %s', ($uni ?? 'null'), $minVersion));

$db = MySqlDB::instance();

$x = Request::pint(key: 'x');
$y = Request::pint(key: 'y');
$id = Request::pint(key: 'id');

$starbase = Request::pstring(key: 's');
$sb = DB::building(name: $starbase, universe: $uni);

if (is_null($sb->starbase)) {
	$sb_loc = $id - ($x * 13) - $y;
    debug('Starbase sb_loc: ' . $sb_loc);
    $sb->starbase = $sb_loc;
    DB::building_update(id: $sb->id, params: ['starbase' => (int)$sb_loc], universe: $uni);
}

debug($sb);

$maparray = explode('~', $mapdata);
debug($maparray);

for ($i = 1; $i < sizeof($maparray); $i++) {
	$temp = explode(',', $maparray[$i]);
    debug($temp);
	// Check to see if we got good data
	if (!strpos($temp[2], "nodata.png") && $temp[0] != 'NaN') {
        debug($temp[0] . ' Does Not Contain "nodata.png"');

        // Check to see if we got Building Info
		if (str_contains($temp[2], "foregrounds")) {
            debug($temp[0] . ' Contains "Foreground" Info');

			$r_bg = 1;
			$r_fg = 2;
			$r_npc = 0;
			// Check to see if we got Background Info
		} elseif (str_contains($temp[2], "backgrounds")) {
            debug($temp[0] . ' Contains "Background" Info Only');

			$r_bg = 2;
			$r_fg = 0;
			$r_npc = 0;
			// Check to see if we got Critter info
		} elseif (str_contains($temp[2], "opponents")) {
            debug($temp[0] . ' Contains "Critter" Info');

			$r_bg = 1;
			$r_fg = 0;
			$r_npc = 2;
			// Must be a Ship or something I don't want
		} elseif (str_contains($temp[2], "xmas-star")) {
            debug($temp[0] . ' Contains "Xmas" Info');

			$r_bg = 1;
			$r_fg = 2;
			$r_npc = 0;
		} else {
            debug($temp[0] . ' Do not care what it contain');

			$r_bg = 1;
			$r_fg = 0;
			$r_npc = 0;
		}

		// Ignore any tile that is energymax.png
		if (str_contains($temp[$r_bg], "background") && strpos($temp[$r_bg], "energymax") != true) {
			// Check to see if we have Info for the current tile
			// Insert new data if there is not current info
			// Do Nothing if there is current info
            $r = DB::map(id: (int)$temp[0], universe: $uni);

			if (!$r) {
				// There is no existing information for the current tile
                debug($temp[0] . ' New Information Inserting into DB');

				$db->addMap($uni, $temp[$r_bg], (int)$temp[0], $sb->id);
                $r = DB::map(id: (int)$temp[0], universe: $uni);
			}

            debug($r);

			if (str_contains($temp[$r_bg], "\\")) {
				$temp[$r_bg] = substr($temp[$r_bg], 0, strpos($temp[$r_bg], "\\"));
			}
			debug($temp[$r_bg]);

			if ($temp[$r_bg] != $r->bg) {
                debug($temp[0] . ' Updating BG Info');
				$db->updateMapBG($uni, $temp[$r_bg], (int)$temp[0]);
			} else {
                debug($temp[0] . ' Not Updating BG Info');
			}

			// Check to see if we have Foreground information for the current tile
			// If we do not then we need to double check for existing info and remove it.
			if ($r_fg != 0) {
                debug($temp[0] . ' Building information exists for current location');

				// Check to See if the DB is NULL
				if (is_null($r->fg)) {
					// DB is NULL Just Add new Info
                    debug($temp[0] . ' Adding BG Info');

					$db->addBuilding($uni, $temp[$r_fg], (int)$temp[0], $sb->id);
				} else {
                    $updateBuilding = [];

					//Test to See if Map and DB match
					if (preg_replace('/[_]tradeoff/', "", $temp[$r_fg]) != preg_replace('/[_]tradeoff/', "", $r->fg)) {
                        debug($temp[0] . ' Foreground info Does Not Matches DB');
                        debug($temp[0] . ' Deleting Old Building');

						// See if we have a Gem merchant
						$db->removeBuilding($uni, (int)$temp[0], 0);

                        debug($temp[0] . ' Inserting New Building');

						$db->addBuilding($uni, $temp[$r_fg], (int)$temp[0], $sb->id);
                        $updateBuilding['starbase'] = 1;
					} else {
                        debug($temp[0] . ' Foreground info Matches DB');

						$db->updateMapFG($uni, $temp[$r_fg], (int)$temp[0]);

						$x = floor(($temp[0] - $sb->starbase) / 13);
						$y = ($temp[0] - ($sb->starbase + ($x * 13)));

                        $updateBuilding['cluster'] = $sb->cluster;
                        $updateBuilding['sector'] = $sb->sector;
                        $updateBuilding['x'] = $x;
                        $updateBuilding['y'] = $y;
					}

                    DB::building_update(id: (int)$temp[0], params: $updateBuilding, universe: $uni);
				}
			} elseif (!(is_null($r->fg))) {
                debug($temp[0] . ' Deleting Foreground info from DB');

				if (strpos($r->fg, "starbase")) {
					$db->removeBuilding($uni, (int)$temp[0], 1);
				} else {
					$db->removeBuilding($uni, (int)$temp[0], 0);
				}
			} else {
                debug($temp[0] . ' No Foreground info to worry about');
			}
		} else {
            debug($temp[0] . ' Energy Max');
		}
	}
}

$db->close();