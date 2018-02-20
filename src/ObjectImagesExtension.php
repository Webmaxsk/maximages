<?php

namespace Webmaxsk\Model\ObjectImagesExtension;

use SilverStripe\ORM\DataExtension;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\LiteralField;
use SilverStripe\AssetAdmin\Forms\UploadField;

class ObjectImagesExtension extends DataExtension {

	private static $allow_images = true;

	private static $db = array(
		'Sorter' => 'Enum("SortOrder, Title, Name, ID")'
	);

	private static $many_many = array(
		'Images' => Image::class
	);

    private static $owns = [
        'Images'
    ];

	private static $many_many_extraFields = array(
		'Images' => array('SortOrder' => 'Int')
	);

	public function updateCMSFields(FieldList $fields) {
		 // Use SortableUploadField instead of UploadField!
		$imagesTab = $fields->findOrMakeTab('Root.Images');

		$owner = $this->owner;
		if ($owner::config()->allow_images) {

			//$uploadClass = (class_exists("SortableUploadField") && $this->owner->Sorter == "SortOrder") ? "SortableUploadField" : "UploadField";
			//$imageField = $uploadClass::create('Images');
            $imageField = UploadField::create('Images');

			$imageField->setFolderName('Uploads/'.$this->owner->ClassName.'/'.$this->owner->ID);
            $imageField->getValidator()->setAllowedExtensions(array('jpg', 'jpeg', 'png', 'gif'));

            $imagesTab->setTitle(_t("Object.IMAGESTAB", "Images"));
            $imageField->setTitle(_t("Object.IMAGESUPLOADLABEL", "Images"));

            if ($this->owner->Sorter == "SortOrder")  {
                $message = (class_exists("SortableUploadField")) ? _t("Object.IMAGESUPLOADHEADING", "<span style='color: green'>Sort images by draging thumbnail</span>") : _t("Object.IMAGESUPLOADHEADINGWRONG", "<span style='color: red'>Sorting images by draging thumbnails (SortOrder) not allowed. Missing module SortabeUploadField.</span>");
            } else {
                $message = _t("Object.IMAGESSORTERNOTICE", "Correct image sorting is visible on frontend only (if Sort by = Title, ID)");
            }

            $dropdownSorter = DropdownField::create('Sorter', _t("Object.IMAGESSORTER", "Sort images by: "))->setSource($this->owner->dbObject('Sorter')->enumValues());
            $fields->addFieldToTab('Root.Images', $dropdownSorter);

            $fields->addFieldToTab('Root.Images', LiteralField::create('ImagesNotice', $message));

			$fields->addFieldToTab('Root.Images', $imageField);
		}
		else
			$fields->removeByName($imagesTab->Name);
	}

	public function SortedImages() {
		return $this->owner->Images()->Sort($this->owner->Sorter);
	}

	public function MainImage() {
		return $this->owner->Images()->Sort($this->owner->Sorter)->limit(1)->First();
	}
}

// EOF
