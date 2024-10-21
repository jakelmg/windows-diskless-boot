<?php
// Hardcoded MAC-to-IQN mapping
$mac_to_iqn = array(
    '00:00:00:00:00:00' => array(
        'initiator_iqn' => 'iqn.2000-01.com.example:computer1',
        'target_iqn' => 'iqn.2000-01.com.iscsiserver:computer1-disk',
    ),
    '11:11:11:11:11:11' => array(
        'initiator_iqn' => 'iqn.2000-01.com.example:computer2',
        'target_iqn' => 'iqn.2000-01.com.iscsiserver:computer2-disk',
    ),
    '22:22:22:22:22:22' => array(
        'initiator_iqn' => 'iqn.2000-01.com.example:computer3',
        'target_iqn' => 'iqn.2000-01.com.iscsiserver:computer3-disk',
    ),
    // Add more MACs and IQNs as needed
);

// Get the MAC address from the query parameter
$mac = $_GET['mac'];

// Check if the MAC address exists in the mapping
if (array_key_exists($mac, $mac_to_iqn)) {
    $initiator_iqn = $mac_to_iqn[$mac]['initiator_iqn'];
    $target_iqn = $mac_to_iqn[$mac]['target_iqn'];

    // Output the iPXE script dynamically
    header('Content-Type: text/plain');
    echo "#!ipxe\n";
    echo "set initiator-iqn {$initiator_iqn}\n";
    echo "set netX/gateway 0.0.0.0\n";
    echo "sanboot -d 0x80 iscsi:1.2.3.4::::{$target_iqn}\n"; // Adjust the IP and other parameters accordingly
} else {
    // If MAC is not found, return an error
    header('Content-Type: text/plain');
    echo "#!ipxe\n";
    echo "echo MAC address not recognized. Retrying...\n";
    echo "sleep 5\n";
    echo "exit 1\n";
}
?>
