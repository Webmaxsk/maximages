<?php
class ObjectImagesExtension extends DataExtension {
	
	private static $db = array(
        'Sorter' => 'Enum("SortOrder, Title, Name, ID")'
    );
	
	private static $many_many = array(
        'Images' => 'Image'
    );
	
	private static $many_many_extraFields = array(
        'Images' => array('SortOrder' => 'Int')
	);
	
	public function updateCMSFields(FieldList $fields) {      
         // Use SortableUploadField instead of UploadField!
	        $limit = 20;
	        $uploadClass = (class_exists("SortableUploadField") && $this->owner->Sorter == "SortOrder") ? "SortableUploadField" : "UploadField";
	        $imageField = $uploadClass::create('Images', _t("Object.IMAGESUPLOADLABEL", "Images"));
	        $imageField->setDescription(sprintf(_t("Object.IMAGESUPLOADLIMIT","Images count limit: %s"), $limit));
			$imageField->setConfig('allowedMaxFileNumber', $limit);
			$imageField->setFolderName('Uploads/'.$this->owner->ClassName.'/'.$this->owner->ID);
			
			$dropdownSorter = DropdownField::create('Sorter', _t("Object.IMAGESSORTER", "Sort imags by: "))->setSource($this->owner->dbObject('Sorter')->enumValues());
			
			if ($this->owner->Sorter == "SortOrder")  {
				$message =(class_exists("SortableUploadField")) ? _t("Object.IMAGESUPLOADHEADING", "<span style='color: green'>Sort images by draging thumbnail</span>") : _t("Object.IMAGESUPLOADHEADINGWRONG", "<span style='color: red'>Sorting images by draging thumbnails (SortOrder) not allowed. Missing module SortabeUploadField.</span>"); 
			} else {
				$message =  _t("Object.IMAGESSORTERNOTICE", "Correct image sorting is visible on frontend only (if Sort by = Title, ID)");
			}
			
			$fields->addFieldToTab('Root.Images', HeaderField::create('ImagesNotice', $message)->setHeadingLevel(4));
			$fields->addFieldToTab('Root.Images', $dropdownSorter);
        	$fields->addFieldToTab('Root.Images', $imageField);
        	
        	$fields->fieldByName('Root.Images')->setTitle(_t("Object.IMAGESTAB", "Images"));
	}
	
	public function SortedImages(){
        return $this->owner->Images()->Sort($this->owner->Sorter);
    }
	
	public function MainImage() {
		return $this->owner->Images()->Sort($this->owner->Sorter)->limit(1)->First();
	}
}

// EOF
