# windows-diskless-boot
Some scripts for diskless booting windows 11 via iSCSI and iPXE based on my experiences doing so for a LinusTechTips video. We got to the stage of getting it working, and spent some time tuning, but as of this stage, performance was not great so I wouldn't recommend it.
However, it did work with the latest Windows 11 build (23H2)

Things you'll need to get this to work:
- Build a WinPE image with drivers for your clients NIC(s) baked in
- Build an iPXE image with an embedded script to break the iPXE chain OR use some fancy DHCP config to do the same (embed.ipxe is an example embedded script)
- To install Windows: Something to host the WinPE files on (a web, or tftp server)
- To install Windows: An SMB share or USB stick plugged into the client computer with Windows install files on it
- Something to host your iPXE scripts on (a web, or tftp server)
- A computer with a NIC that can handle iPXE sanbooting (Aquantia/Marvell 10G ones don't work at all. Realtek 2.5G works. Mellanox ConnectX works. Intel X540 works.
- Optional: a PHP enabled web server to direct clients to a specific iSCSI target based on their MAC address (useful if you have multiple clients)

BIOS stuff:
- Enable Network Stack ("IPv4 PXE" it's commonly called)
- Enable CSM (some BIOS wont allow/show the PXE setting above until CSM is enabled)
- Under CSM, set Network booting to UEFI/EFI only for Windows 11 ideally.

Random stuff:
- Use a 512 sector size if you're booting LEGACY, 4k will not work
- A dedicated NIC / network for iSCSI using jumbo frames is strongly recommended

Additional resources:

iPXE: https://ipxe.org/ 
- You will need to build your own iPXE image with an embedded script in it that boots what you want, otherwise it boot loops iPXE forever. 
    - Building iPXE: https://ipxe.org/appnote/buildtargets
    - Embedded Script info: https://ipxe.org/howto/chainloading#breaking_the_loop_with_an_embedded_script
    - Alternatively, you can use some fancy DHCP stuffs to chainload the correct iPXE boot script https://ipxe.org/howto/chainloading#breaking_the_loop_with_the_dhcp_server
- I had the most success with snponly.efi (stripped down version of iPXE for efi). The full ipxe.efi doesn't load on some NICs because its too big. You can also build iPXE with drivers specific to your NIC which worked great for me on some computers, and not at all on others. If you're having trouble try snponly.efi build, OR boot iPXE off a USB stick to start.
- When building iPXE you can enable debugging info, which is VERY helpful if you're having issues. https://ipxe.org/download#debug_builds


WinPE Image Builder: https://github.com/cmartinezone/WinPEBuilder (automatically includes the packages needed)
How to add drivers to WinPE image: https://forums.ivanti.com/s/article/How-To-Use-DISM-to-Manually-Inject-Drivers-into-the-Boot-wim?language=en_US

How to disable page file (if experiencing IRQ NOT LESS THAN EQUAL BSOD): 
- In regedit, load hive \Windows\system32\config\system under HKEY_LOCAL_MACHINE
- Within the loaded hive, locate the PagingFiles key under \SYSTEM\ControlSet001\Control\Session Manager\Memory Management and blank the string.
- Unload the hive.