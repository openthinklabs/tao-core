<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA;
 */

namespace oat\tao\scripts\tools\import;

use oat\oatbox\extension\script\ScriptAction;
use oat\generis\model\OntologyAwareTrait;

/**
 * sudo -u www-data php index.php 'oat\tao\scripts\tools\import\assignUser2GroupCsv'  -f /txt.csv
 */
class AssignUser2GroupCsv extends ScriptAction
{
    use OntologyAwareTrait;

    protected function provideOptions()
    {
        return [
            'file-path' => [
                'prefix' => 'f',
                'longPrefix' => 'file-path',
                'required' => true,
                'description' => 'File path location.',
            ],
        ];
    }

    protected function provideDescription()
    {
        return 'Assign test taker to a group.';
    }

    /**
     * @return \common_report_Report
     * @throws \common_exception_NotFound
     * @throws \oat\oatbox\service\exception\InvalidService
     * @throws \oat\oatbox\service\exception\InvalidServiceManagerException
     */
    protected function run()
    {
        //query users di cbt launcher, ambil username dan group resource id nya (?)

        //cek user di tao,  jika ada, assign ke group
        //SELECT subject as testtaker_resource_id, object as nisn FROM `statements`  where predicate='http://www.tao.lu/Ontologies/generis.rdf#login';

        $values   = ['http://tao.local/mytao.rdf#i627a6bfba5d4b7786508abd5462215c11']; //Group Resource Identifier
        $resource = $this->getResource('http://tao.local/mytao.rdf#i627c3f6e82dc53243600e61d5f9b33bfd5'); //Test Taker Resource Identifier
        $property = $this->getProperty('http://www.tao.lu/Ontologies/TAOGroup.rdf#member'); 
        $resource->editPropertyValues($property, $values);        
    }
}
