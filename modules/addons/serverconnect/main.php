<?php

use WHMCS\Database\Capsule;

/**
 * ServerConnect for WHMCS
 *
 * An alternative for WHMCS Connect that actually works in Google Chrome.
 * 
 * Same functionality (filtering, sorting by groups, server logos).
 * 
 * Left click the server to open it in the same tab, right click to open in a new tab. 
 *
 * @package    WHMCS
 * @author     Lee Mahoney <lee@leemahoney.dev>
 * @copyright  Copyright (c) Lee Mahoney 2022
 * @license    MIT License
 * @version    0.0.1
 * @link       https://leemahoney.dev
 */

# Prevent direct loading of the script
if (!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="/modules/addons/serverconnect/assets/css/app.css">
    </head>
    <body>
        
        <header class="sc-header">
            <h1><i class="fas fa-server"></i> ServerConnect for WHMCS</h1>
            <div class="search-container">
                <input type="text" name="search" class="search" placeholder="Begin typing server name...">
                <button class="clear-button"><i class="fas fa-times-circle"></i></button>
            </div>
        </header>

        <section class="sc-container">
            <div class="main">

                <?php

                # Need to set this up to check for unallocated servers later on
                $allServers         = [];
                $allocatedServers   = [];
                $unallocatedServers = [];

                # Populate the allServers array with all active servers
                $grabAllServers = Capsule::table('tblservers')->where(['disabled' => 0])->get();

                foreach (Capsule::table('tblservers')->where(['disabled' => 0])->get('id') as $theServer) {
                    $allServers[] = $theServer->id;
                }

                # Grab the server groups
                $serverGroups = Capsule::table('tblservergroups')->orderBy('name')->get(['id', 'name'])->all();

                # Loop through each group
                foreach ($serverGroups as $serverGroup) {

                    echo "<div class=\"server-group\">";
                        
                        echo "<h2><i class=\"far fa-archive\"></i> {$serverGroup->name}</h2>";
                    
                        echo "<div class=\"server-list\">";

                            # Get the serverid for each server in the parent group
                            $serversInGroup = Capsule::table('tblservergroupsrel')->where(['groupid' => $serverGroup->id])->get(['serverid'])->all();

                            # Display a friendly message if no active servers in group (could also add this check just after the foreach to hide the group completely)
                            if (count($serversInGroup) === 0) {
                                echo "<p class='alert alert-danger'>No servers currently in the <b>{$serverGroup->name}</b> group.</p>";
                            } else {

                                # Let's get each server from the tblservers table by their ID
                                foreach ($serversInGroup as $server) {

                                    # Again, this will help with the unallocated servers later
                                    $allocatedServers[] = $server->serverid;

                                    # Grab the server data that we need from the database
                                    $serverData = Capsule::table('tblservers')->where(['id' => $server->serverid, 'disabled' => 0])->orderBy('name')->get(['id', 'name', 'type', 'hostname', 'ipaddress'])->first();

                                    # Make sure we get something back, otherwise a while blank tile will show
                                    if ($serverData) {
                                        $serverId       = $serverData->id;
                                        $serverName     = $serverData->name;
                                        $serverLogo     = "/modules/servers/{$serverData->type}/logo.png";
                                        $serverHostname = $serverData->hostname ? $serverData->hostname : $serverData->ipaddress;

                                        
                                        # Print the tile with the server data
                                        echo "<div class=\"server-item\" data-server-id=\"{$serverId}\">
                                                <img src=\"{$serverLogo}\" alt=\"\">
                                                <h3>{$serverName}</h3>
                                                <p>{$serverHostname}</p>
                                              </div>";
                                    }

                                }

                            }
                        
                        echo "</div>";

                    echo "</div>";

                }

                # Let's compare both the allServers array and the allocatedServers array to see if theres a difference, if so then we have unallocated servers
                $unallocatedServers = array_diff($allServers, $allocatedServers);

                # ^ As I said above, if not empty then we have some stray servers hanging around!
                if(!empty($unallocatedServers)) {

                    echo "<div class=\"server-group\">";
                        echo "<h2><i class=\"far fa-exclamation-circle\"></i> Unallocated Servers</h2>";

                        echo "<div class=\"server-list\">";

                        # Grab their ID's so we can extract the server details from the database
                        foreach ($unallocatedServers as $unallocatedServerId) {

                            $serverData = Capsule::table('tblservers')->where(['id' => $unallocatedServerId, 'disabled' => 0])->orderBy('name')->get(['id', 'name', 'type', 'hostname', 'ipaddress'])->first();

                            $serverId       = $serverData->id;
                            $serverName     = $serverData->name;
                            $serverLogo     = "/modules/servers/{$serverData->type}/logo.png";
                            $serverHostname = $serverData->hostname ? $serverData->hostname : $serverData->ipaddress;

                            
                            # Print the tile with the server data
                            echo "<div class=\"server-item\" data-server-id=\"{$serverId}\">
                                    <img src=\"{$serverLogo}\" alt=\"\">
                                    <h3>{$serverName}</h3>
                                    <p>{$serverHostname}</p>
                                  </div>";


                        }

                        echo "</div>";

                    echo "</div>";

                }
                
                ?>

            </div>  
        </section>

        <footer class="footer">
            <p>Made with <i class="fas fa-heart"></i> by <a href="https://leemahoney.dev/">Lee Mahoney</a></p>
        </footer>

        <script src="/modules/addons/serverconnect/assets/js/jquery.highlight.js" defer></script>
        <script src="/modules/addons/serverconnect/assets/js/app.js"></script>
        <script>
            function redirectToServer(serverId) {
                window.open('configservers.php?action=singlesignon&token=<?php echo generate_token('link'); ?>&serverid=' + serverId, '_self');
            }

            function redirectToServerNewTab(serverId) {
                window.open('configservers.php?action=singlesignon&token=<?php echo generate_token('link'); ?>&serverid=' + serverId, '_blank').focus();
            }
        </script>
        
    </body>
</html>