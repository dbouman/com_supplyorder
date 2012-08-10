#!/bin/sh

filename='com_supplyorder.zip'
dir='.';

rm $filename 2>/dev/null

zip -r $filename $dir/administrator/ $dir/component/ $dir/images $dir/install.supplyorder.php $dir/supplyorder.xml $dir/uninstall.supplyorder.php 1>/dev/null

echo $filename 'has been successfully created.';
