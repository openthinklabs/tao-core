<?php

error_reporting(E_ALL);

/**
 * The Service class is an abstraction of each service instance. 
 * Used to centralize the behavior related to every servcie instances.
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * Service is the base class of all services, and implements the singleton
 * for derived services
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('tao/models/classes/class.Service.php');

/* user defined includes */
// section 10-13-1-45-792423e0:12398d13f24:-8000:0000000000001834-includes begin
// section 10-13-1-45-792423e0:12398d13f24:-8000:0000000000001834-includes end

/* user defined constants */
// section 10-13-1-45-792423e0:12398d13f24:-8000:0000000000001834-constants begin
// section 10-13-1-45-792423e0:12398d13f24:-8000:0000000000001834-constants end

/**
 * The Service class is an abstraction of each service instance. 
 * Used to centralize the behavior related to every servcie instances.
 *
 * @abstract
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 * @subpackage models_classes
 */
abstract class tao_models_classes_GenerisService
    extends tao_models_classes_Service
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * constructor
     *
     * @access protected
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return void
     */
    protected function __construct()
    {
        // section 10-13-1-45-792423e0:12398d13f24:-8000:000000000000183D begin
        // section 10-13-1-45-792423e0:12398d13f24:-8000:000000000000183D end
    }

    /**
     * Enable you to  load the RDF ontologies.
     *
     * @access protected
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @deprecated
     * @param  array ontologies
     * @return mixed
     * @see core_kernel_classes_Session::_construct
     */
    protected function loadOntologies($ontologies)
    {
        // section 127-0-1-1-266d5677:1246ba0ab68:-8000:0000000000001A9B begin

   		$myOntologies = array_merge($this->ontologies, $ontologies);
		if(count($myOntologies) > 0){
			$session = core_kernel_classes_Session::singleton();
			foreach($myOntologies as $ontology){
				$session->model->loadModel($ontology);
			}
		}

        // section 127-0-1-1-266d5677:1246ba0ab68:-8000:0000000000001A9B end
    }

    /**
     * search the instances matching the filters in parameters
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  array propertyFilters
     * @param  Class topClazz
     * @param  array options
     * @return array
     */
    public function searchInstances($propertyFilters = array(),  core_kernel_classes_Class $topClazz = null, $options = array())
    {
        $returnValue = array();

        // section 127-0-1-1-106f2734:126b2f503d0:-8000:0000000000001E96 begin

        if(!is_null($topClazz)){
        	$returnValue = $topClazz->searchInstances($propertyFilters, $options);
        }


        // section 127-0-1-1-106f2734:126b2f503d0:-8000:0000000000001E96 end

        return (array) $returnValue;
    }

    /**
     * Get the class of the resource in parameter (the rdfs type property)
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource instance
     * @return core_kernel_classes_Class
     */
    public function getClass( core_kernel_classes_Resource $instance)
    {
        $returnValue = null;

        // section 127-0-1-1--519643a:127850ba1cf:-8000:000000000000233B begin

     	if(!is_null($instance)){
        	if(!$instance->isClass() && !$instance->isProperty()){
        		foreach($instance->getTypes() as $type){
        			$returnValue = $type;
        			break;
        		}
        	}
        }

        // section 127-0-1-1--519643a:127850ba1cf:-8000:000000000000233B end

        return $returnValue;
    }

    /**
     * Retrieve a property
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Class clazz
     * @param  string label
     * @return core_kernel_classes_Property
     */
    public function getPropertyByLabel( core_kernel_classes_Class $clazz, $label)
    {
        $returnValue = null;

        // section 10-13-1-45-2836570e:123bd13e69b:-8000:000000000000187B begin

   		if(strlen(trim($label)) == 0){
			throw new Exception("Please, never use empty labels!");
		}
		foreach($clazz->getProperties() as $property){
			if($property->getLabel() == $label){
				$returnValue = $property;
				break;
			}
		}

        // section 10-13-1-45-2836570e:123bd13e69b:-8000:000000000000187B end

        return $returnValue;
    }

    /**
     * Instantiate an RDFs Class
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Class clazz
     * @param  string label
     * @return core_kernel_classes_Resource
     */
    public function createInstance( core_kernel_classes_Class $clazz, $label = '')
    {
        $returnValue = null;

        // section 10-13-1-45--135fece8:123b76cb3ff:-8000:0000000000001897 begin

        if( empty($label) ){
			$label =  $this->createUniqueLabel($clazz);
		}

		$returnValue = core_kernel_classes_ResourceFactory::create($clazz, $label, '');

        // section 10-13-1-45--135fece8:123b76cb3ff:-8000:0000000000001897 end

        return $returnValue;
    }

    /**
     * Short description of method createUniqueLabel
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Class clazz
     * @param  boolean subClassing
     * @return string
     */
    public function createUniqueLabel( core_kernel_classes_Class $clazz, $subClassing = false)
    {
        $returnValue = (string) '';

        // section 127-0-1-1-5449e54e:12a6a9d50dc:-8000:0000000000002487 begin


        if($subClassing){
        	$labelBase = $clazz->getLabel() . '_' ;
        	$count = count($clazz->getSubClasses()) +1;
        }
        else{
        	$labelBase = $clazz->getLabel() . ' ' ;
        	$count = count($clazz->getInstances()) +1;
        }

		$options = array(
			'lang' 				=> core_kernel_classes_Session::singleton()->getDataLanguage(),
			'like' 				=> false,
			'recursive'  		=> false
		);

		do{
			$exist = false;
			$label =  $labelBase . $count;
			$result = $clazz->searchInstances(array(RDFS_LABEL => $label), $options);
			if(count($result) > 0){
				$exist = true;
				$count ++;
			}
		} while($exist);

		$returnValue = $label;

        // section 127-0-1-1-5449e54e:12a6a9d50dc:-8000:0000000000002487 end

        return (string) $returnValue;
    }

    /**
     * Subclass an RDFS Class
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Class parentClazz
     * @param  string label
     * @return core_kernel_classes_Class
     */
    public function createSubClass( core_kernel_classes_Class $parentClazz, $label = '')
    {
        $returnValue = null;

        // section 127-0-1-1-404a280c:12475f095ee:-8000:0000000000001AB5 begin

        if( empty($label) ){
			$label = $this->createUniqueLabel($parentClazz, true);
		}
		$returnValue = $parentClazz->createSubClass($label, '');

        // section 127-0-1-1-404a280c:12475f095ee:-8000:0000000000001AB5 end

        return $returnValue;
    }

    /**
     * bind the given RDFS properties to the RDFS resource in parameter
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource instance
     * @param  array properties
     * @return core_kernel_classes_Resource
     */
    public function bindProperties( core_kernel_classes_Resource $instance, $properties = array())
    {
        $returnValue = null;

        // section 10-13-1-45--135fece8:123b76cb3ff:-8000:00000000000018A5 begin
        foreach($properties as $propertyUri => $propertyValue){

        	if($propertyUri == RDF_TYPE){
        		foreach($instance->getTypes() as $type){
        			$instance->removeType($type);
        		}
        		if(!is_array($propertyValue)){
        			$types = array($propertyValue) ;
        		}
        		foreach($types as $type){
        			$instance->setType(new core_kernel_classes_Class($type));
        		}
        		continue;
        	}

			$prop = new core_kernel_classes_Property( $propertyUri );
			$values = $instance->getPropertyValuesCollection($prop);
			if($values->count() > 0){
				if(is_array($propertyValue)){
					$instance->removePropertyValues($prop);
					foreach($propertyValue as $aPropertyValue){
						$instance->setPropertyValue(
							$prop,
							$aPropertyValue
						);
					}

				}
				else{
					$instance->editPropertyValues(
						$prop,
						$propertyValue
					);
					if(strlen(trim($propertyValue))==0){
						//if the property value is an empty space(the default value in a select input field), delete the corresponding triplet (and not all property values)
						$instance->removePropertyValues($prop, array('pattern' => ''));
					}
				}
			}
			else{

				if(is_array($propertyValue)){

					foreach($propertyValue as $aPropertyValue){
						$instance->setPropertyValue(
							$prop,
							$aPropertyValue
						);
					}
				}
				else{
					$instance->setPropertyValue(
						$prop,
						$propertyValue
					);
				}
			}
		}

        $returnValue = $instance;

        // section 10-13-1-45--135fece8:123b76cb3ff:-8000:00000000000018A5 end

        return $returnValue;
    }

    /**
     * duplicate a resource
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource instance
     * @param  Class clazz
     * @return core_kernel_classes_Resource
     */
    public function cloneInstance( core_kernel_classes_Resource $instance,  core_kernel_classes_Class $clazz = null)
    {
        $returnValue = null;

        // section 127-0-1-1-50de96c6:1266ae198e7:-8000:0000000000001E30 begin

   		$returnValue = $this->createInstance($clazz);
		if(!is_null($returnValue)){
			$properties = $clazz->getProperties(true);
			foreach($properties as $property){
				foreach($instance->getPropertyValues($property) as $propertyValue){
					// Avoid doublons, the RDF TYPE property will be set by the implementation layer
					if ($property->uriResource == RDF_TYPE && $propertyValue == $clazz->uriResource){
						continue;
					}
					$returnValue->setPropertyValue($property, $propertyValue);
				}
			}
			$label = $instance->getLabel();
			$cloneLabel = "$label bis";
			if(preg_match("/bis/", $label)){
				$cloneNumber = (int)preg_replace("/^(.?)*bis/", "", $label);
				$cloneNumber++;
				$cloneLabel = preg_replace("/bis(.?)*$/", "", $label)."bis $cloneNumber" ;
			}

			$returnValue->setLabel($cloneLabel);
		}

        // section 127-0-1-1-50de96c6:1266ae198e7:-8000:0000000000001E30 end

        return $returnValue;
    }

    /**
     * Clone a Class and move it under the newParentClazz
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Class sourceClazz
     * @param  Class newParentClazz
     * @param  Class topLevelClazz
     * @return core_kernel_classes_Class
     */
    public function cloneClazz( core_kernel_classes_Class $sourceClazz,  core_kernel_classes_Class $newParentClazz = null,  core_kernel_classes_Class $topLevelClazz = null)
    {
        $returnValue = null;

        // section 127-0-1-1-6c3e90c1:1288272e8b7:-8000:0000000000001F3F begin

    	if(!is_null($sourceClazz) && !is_null($newParentClazz)){
        	if((is_null($topLevelClazz))){
        		$properties = $sourceClazz->getProperties(false);
        	}
        	else{
        		$properties = $this->getClazzProperties($sourceClazz, $topLevelClazz);
        	}

        	//check for duplicated properties
        	$newParentProperties = $newParentClazz->getProperties(true);
        	foreach($properties as $index => $property){
        		foreach($newParentProperties as $newParentProperty){
        			if($property->uriResource == $newParentProperty->uriResource){
        				unset($properties[$index]);
        				break;
        			}
        		}
        	}

        	//create a new class
        	$returnValue = $this->createSubClass($newParentClazz, $sourceClazz->getLabel());

        	//assign the properties of the source class
        	foreach($properties as $property){
        		$property->setDomain($returnValue);
        	}
        }

        // section 127-0-1-1-6c3e90c1:1288272e8b7:-8000:0000000000001F3F end

        return $returnValue;
    }

    /**
     * Change the Class (RDFS_TYPE) of a resource
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource instance
     * @param  Class destinationClass
     * @return boolean
     */
    public function changeClass( core_kernel_classes_Resource $instance,  core_kernel_classes_Class $destinationClass)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--4b0a5ad3:12776b15903:-8000:0000000000002331 begin

   		try{
        	foreach($instance->getTypes() as $type){
        		$instance->removeType($type);
        	}
        	$instance->setType($destinationClass);
        	foreach($instance->getTypes() as $type){
        		if($type->uriResource == $destinationClass->uriResource){
        			$returnValue = true;
        			break;
        		}
        	}
        }
        catch(common_Exception $ce){
        	print $ce;
        }

        // section 127-0-1-1--4b0a5ad3:12776b15903:-8000:0000000000002331 end

        return (bool) $returnValue;
    }

    /**
     * Get all the properties of the class in parameter.
     * The properties are taken recursivly into the class parents up to the top
     * class.
     * If the top level class is not defined, we used the TAOObject class.
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Class clazz
     * @param  Class topLevelClazz
     * @return array
     */
    public function getClazzProperties( core_kernel_classes_Class $clazz,  core_kernel_classes_Class $topLevelClazz = null)
    {
        $returnValue = array();

        // section 127-0-1-1--250780b8:12843f3062f:-8000:0000000000002405 begin

        if(is_null($topLevelClazz)){
			$topLevelClazz = new core_kernel_classes_Class(TAO_OBJECT_CLASS);
		}

		if($clazz->uriResource == $topLevelClazz->uriResource){
			$returnValue = $clazz->getProperties(false);
			return (array) $returnValue;
		}

		//determine the parent path
		$parents = array();
		$top = false;
		do{
			if(!isset($lastLevelParents)){
				$parentClasses = $clazz->getParentClasses(false);
			}
			else{
				$parentClasses = array();
				foreach($lastLevelParents as $parent){
					$parentClasses = array_merge($parentClasses, $parent->getParentClasses(false));
				}
			}
			if(count($parentClasses) == 0){
				break;
			}
			$lastLevelParents = array();
			foreach($parentClasses as $parentClass){
				if($parentClass->uriResource == $topLevelClazz->uriResource ) {
					$parents[$parentClass->uriResource] = $parentClass;
					$top = true;
					break;
				}
				if($parentClass->uriResource == RDF_CLASS){
					continue;
				}

				$allParentClasses = $parentClass->getParentClasses(true);
				if(array_key_exists($topLevelClazz->uriResource, $allParentClasses)){
					 $parents[$parentClass->uriResource] = $parentClass;
				}
				$lastLevelParents[$parentClass->uriResource] = $parentClass;
			}
		}while(!$top);

		foreach($parents as $parent){
			$returnValue = array_merge($returnValue, $parent->getProperties(false));
    	}

		$returnValue = array_merge($returnValue, $clazz->getProperties(false));

        // section 127-0-1-1--250780b8:12843f3062f:-8000:0000000000002405 end

        return (array) $returnValue;
    }

    /**
     * get the properties of the source class that are not in the destination
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Class sourceClass
     * @param  Class destinationClass
     * @return array
     */
    public function getPropertyDiff( core_kernel_classes_Class $sourceClass,  core_kernel_classes_Class $destinationClass)
    {
        $returnValue = array();

        // section 127-0-1-1--4b0a5ad3:12776b15903:-8000:0000000000002337 begin

    	$sourceProperties = $sourceClass->getProperties(true);
        $destinationProperties = $destinationClass->getProperties(true);

        foreach($sourceProperties as $sourcePropertyUri => $sourceProperty){
        	if(!array_key_exists($sourcePropertyUri, $destinationProperties)){
        		array_push($returnValue, $sourceProperty);
        	}
        }

        // section 127-0-1-1--4b0a5ad3:12776b15903:-8000:0000000000002337 end

        return (array) $returnValue;
    }

    /**
     * get the properties of an instance for a specific language
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource instance
     * @param  string lang
     * @return array
     */
    public function getTranslatedProperties( core_kernel_classes_Resource $instance, $lang)
    {
        $returnValue = array();

        // section 127-0-1-1--1254e308:126aced7510:-8000:0000000000001E84 begin

    	try{
			foreach($instance->getTypes() as $clazz){
				foreach($clazz->getProperties(true) as $property){

					if($property->isLgDependent() || $property->uriResource == RDFS_LABEL){
						$collection = $instance->getPropertyValuesByLg($property, $lang);
						if($collection->count() > 0 ){

							if($collection->count() == 1){
								$returnValue[$property->uriResource] = (string)$collection->get(0);
							}
							else{
								$propData = array();
								foreach($collection->getIterator() as $collectionItem){
									$propData[] = (string)$collectionItem;
								}
								$returnValue[$property->uriResource] = $propData;
							}
						}
					}
				}
			}
		}
		catch(Exception $e){
			print $e;
		}

        // section 127-0-1-1--1254e308:126aced7510:-8000:0000000000001E84 end

        return (array) $returnValue;
    }

    /**
     * set the properties of an instance for a specific language
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource instance
     * @param  string lang
     * @param  array data
     * @return boolean
     */
    public function setTranslatedProperties( core_kernel_classes_Resource $instance, $lang, $data)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--1254e308:126aced7510:-8000:0000000000001E88 begin
        // section 127-0-1-1--1254e308:126aced7510:-8000:0000000000001E88 end

        return (bool) $returnValue;
    }

    /**
     * Format an RDFS Class to an array
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Class clazz
     * @return array
     */
    public function toArray( core_kernel_classes_Class $clazz)
    {
        $returnValue = array();

        // section 127-0-1-1-1f98225a:12544a8e3a3:-8000:0000000000001C80 begin

    	$properties = $clazz->getProperties(false);
		foreach($clazz->getInstances(false) as $instance){
			$data = array();
			foreach($properties	as $property){

				$data[$property->getLabel()] = null;

				$values = $instance->getPropertyValues($property);
				if(count($values) > 1){
					$data[$property->getLabel()] = $values;
				}
				elseif(count($values) == 1){
					$data[$property->getLabel()] = $values[0];
				}
			}
			array_push($returnValue, $data);
		}

        // section 127-0-1-1-1f98225a:12544a8e3a3:-8000:0000000000001C80 end

        return (array) $returnValue;
    }

    /**
     * Format an RDFS Class to an array to be interpreted by the client tree
     * This is a closed array format.
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Class clazz
     * @param  array options
     * @return array
     */
    public function toTree( core_kernel_classes_Class $clazz, $options)
    {
        $returnValue = array();

        // section 127-0-1-1-404a280c:12475f095ee:-8000:0000000000001A9B begin
		$subclasses = (isset($options['subclasses'])) ? $options['subclasses'] : true;
		$instances = (isset($options['instances'])) ? $options['instances'] : true;
		$highlightUri = (isset($options['highlightUri'])) ? $options['highlightUri'] : '';
		$labelFilter = (isset($options['labelFilter'])) ? $options['labelFilter'] : '';
		$recursive = (isset($options['recursive'])) ? $options['recursive'] : false;
		$chunk = (isset($options['chunk'])) ? $options['chunk'] : false;
		$browse = (isset($options['browse'])) ? $options['browse'] : array();
		$offset = (isset($options['offset'])) ? $options['offset'] : 0;
		$limit = (isset($options['limit'])) ? $options['limit'] : 0;

		$instancesData = array();
		$isFiltering = false;
		if (!empty($labelFilter) && $labelFilter!='*') {
			$isFiltering = true;
			$subclasses = false;
		}

		if ($instances) {
			$getInstancesOptions = array();
			$getInstancesOptions = array_merge($getInstancesOptions, array(
				'limit'  => $limit,
				'offset' => $offset
			));

			// Make a recursive search if a filter has been given => the result will be a one dimension array
			$searchResult = $clazz->getInstances($isFiltering, $getInstancesOptions);

			foreach ($searchResult as $instance){
				$label = $instance->getLabel();
				if (empty($label)) {
					$label = $instance->uriResource;
				}

				$instanceData = array(
						'data' 	=> tao_helpers_Display::textCutter($label, 16),
						'type'	=> 'instance',
						'attributes' => array(
							'id' => tao_helpers_Uri::encode($instance->uriResource),
							'class' => 'node-instance'
						)
					);
				if (strlen($labelFilter) > 0) {
					if (preg_match("/^".str_replace('*', '(.*)', $labelFilter."$/mi"), trim($label))) {
						$instancesData[] = $instanceData;
					}
				} else {
					$instancesData[] = $instanceData;
				}
			}
		}

		$subclassesData = array();
		if ($subclasses) {
			foreach ($clazz->getSubClasses(false) as $subclass) {
				$options['recursive'] = true;
				$options['chunk'] = false;
				$subclassesData[] = $this->toTree($subclass, $options);
			}
		}

		//format classes for json tree datastore
		$data = array();
		if (!$chunk) {
			$label = $clazz->getLabel();
			if (empty($label)) {
				$label = $clazz->uriResource;
			} else {
				$label = tao_helpers_Display::textCutter($label, 16);
			}

			$data = array(
				'data' 	=> $label,
				'type'	=> 'class',
				'count' => (int) $clazz->countInstances(),
				'attributes' => array(
						'id' => tao_helpers_Uri::encode($clazz->uriResource),
						'class' => 'node-class'
					)
 			);
		}

		$children = array_merge($subclassesData, $instancesData);
		if (count($children) > 0) {
			if (($highlightUri != '' && $recursive)) {
				foreach ($children as $child) {
					if ($child['attributes']['id'] == $highlightUri) {
						$recursive = false;
						break;
					}
				}
			}
			if ($recursive) {
				if (!$chunk) {
					$data['children'] = array();
				}
				if (count($children) > 0) {
					$data['state'] = 'closed';
				}
			} else {
				if ($chunk) {
					$data = $children;
				} else {
					$data['children'] = $children;
				}
			}
		}
		if ($highlightUri != '') {
			$highlightedResource = new core_kernel_classes_Resource(tao_helpers_Uri::decode($highlightUri));
			if (!$highlightedResource->isClass()) {
				$parentClassUris = array();
				foreach ($resourceClasses = $highlightedResource->getTypes() as $resourceClass) {
					$parentClassUris = array_merge(
						$parentClassUris,
						tao_helpers_Uri::encodeArray(array_keys($resourceClass->getParentClasses(true)), tao_helpers_Uri::ENCODE_ARRAY_VALUES)
					);
				}
				if (isset($data['attributes'])) {
					if (in_array($data['attributes']['id'], $parentClassUris)) {
						$data['state'] = 'open';
						$data['children'] = $children;
					}
				}
				if (isset($data['children'])) {
					foreach ($data['children'] as $index => $child) {
						if (in_array($child['attributes']['id'], $parentClassUris)) {
							$data['children'][$index]['state'] = 'open';
						}
					}
				}
			}
		}
		if (count($browse) > 0) {
			if (isset($data['attributes'])) {
				if (in_array($data['attributes']['id'], $browse)) {
					$data['state'] = 'open';
					$data['children'] = $children;
				}
			}
		}
		$returnValue = $data;
        // section 127-0-1-1-404a280c:12475f095ee:-8000:0000000000001A9B end

        return (array) $returnValue;
    }

    /**
     * Short description of method getTaoObjectFolder
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string extension
     * @param  Resource object
     * @return string
     */
    public function getTaoObjectFolder($extension,  core_kernel_classes_Resource $object)
    {
        $returnValue = (string) '';

        // section 127-0-1-1-2450a56a:134227b7ec6:-8000:000000000000346A begin
        // section 127-0-1-1-2450a56a:134227b7ec6:-8000:000000000000346A end

        return (string) $returnValue;
    }

} /* end of abstract class tao_models_classes_GenerisService */

?>