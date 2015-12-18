<?php

interface XCache_interface
{

    public function readCache($type, $name, $ID, $onlyCheck = FALSE);

    public function writeCache($type, $name, $ID, $output, $depID = "");

    public function deleteCache($type, $name = '', $ID = '');

    public function cleanCache();

    public function getCacheInfo($type = NULL);

    public function getCacheMetadata($id);

    public function isSupported($driver);

    public function getInstance();

    public function setOptions($options);
}
