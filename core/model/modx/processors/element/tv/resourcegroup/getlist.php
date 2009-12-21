<?php
/**
 * Gets a list of resource groups associated to a TV.
 *
 * @param integer $tv The ID of the TV
 *
 * @param integer $start (optional) The record to start at. Defaults to 0.
 * @param integer $limit (optional) The number of records to limit to. Defaults
 * to 20.
 * @param string $sort (optional) The column to sort by. Defaults to name.
 * @param string $dir (optional) The direction of the sort. Defaults to ASC.
 *
 * @package modx
 * @subpackage processors.element.template.tv.resourcegroup
 */
if (!$modx->hasPermission('view')) return $modx->error->failure($modx->lexicon('permission_denied'));
$modx->lexicon->load('tv');

/* setup default properties */
$isLimit = !empty($_REQUEST['limit']);
$start = $modx->getOption('start',$_REQUEST,0);
$limit = $modx->getOption('limit',$_REQUEST,20);
$sort = $modx->getOption('sort',$_REQUEST,'name');
$dir = $modx->getOption('dir',$_REQUEST,'ASC');
$tv = $modx->getOption('tv',$_REQUEST,false);

/* query for resource groups */
$c = $modx->newQuery('modResourceGroup');
$c->sortby($sort,$dir);
if ($isLimit) $c->limit($limit,$start);
$resourceGroups = $modx->getCollection('modResourceGroup',$c);
$count = $modx->getCount('modResourceGroup');

/* iterate through groups */
$list = array();
foreach ($resourceGroups as $resourceGroup) {
    if ($tv) {
        $rgtv = $modx->getObject('modTemplateVarResourceGroup',array(
            'tmplvarid' => $tv,
            'documentgroup' => $resourceGroup->get('id'),
        ));
    } else $rgtv = null;
    $resourceGroup->set('access',($rgtv ? true : false));

    $resourceGroupArray = $resourceGroup->toArray();
    $resourceGroupArray['menu'] = array();
    $list[] = $resourceGroupArray;
}

return $this->outputArray($list,$count);