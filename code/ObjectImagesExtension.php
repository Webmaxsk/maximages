<?php

class ObjectImagesExtension extends DataExtension {

	private static $allow_images = true;
	private static $images_count = 20;

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
		$imagesTab = $fields->findOrMakeTab('Root.Images');

		$owner = $this->owner;
		if ($owner::config()->allow_images) {
			$limit = $owner::config()->images_count;

			$uploadClass = (class_exists("SortableUploadField") && $this->owner->Sorter == "SortOrder") ? "SortableUploadField" : "UploadField";

			$imageField = $uploadClass::create('Images');
			$imageField->setConfig('allowedMaxFileNumber', $limit);
			$imageField->setFolderName('Uploads/'.$this->owner->ClassName.'/'.$this->owner->ID);

			if ($limit==1) {
				$imagesTab->setTitle(_t("Object.IMAGETAB", "Images"));
				$imageField->setTitle(_t("Object.IMAGEUPLOADLABEL", "Image"));
			}
			else {
				$imagesTab->setTitle(_t("Object.IMAGESTAB", "Images"));
				$imageField->setTitle(_t("Object.IMAGESUPLOADLABEL", "Images"));
				$imageField->setDescription(sprintf(_t("Object.IMAGESUPLOADLIMIT","Images count limit: %s"), $limit));

				if ($this->owner->Sorter == "SortOrder")  {
					$message = (class_exists("SortableUploadField")) ? _t("Object.IMAGESUPLOADHEADING", "<span style='color: green'>Sort images by draging thumbnail</span>") : _t("Object.IMAGESUPLOADHEADINGWRONG", "<span style='color: red'>Sorting images by draging thumbnails (SortOrder) not allowed. Missing module SortabeUploadField.</span>");
				} else {
					$message = _t("Object.IMAGESSORTERNOTICE", "Correct image sorting is visible on frontend only (if Sort by = Title, ID)");
				}

				$dropdownSorter = DropdownField::create('Sorter', _t("Object.IMAGESSORTER", "Sort images by: "))->setSource($this->owner->dbObject('Sorter')->enumValues());
				$fields->addFieldToTab('Root.Images', $dropdownSorter);

				$fields->addFieldToTab('Root.Images', HeaderField::create('ImagesNotice', $message)->setHeadingLevel(4));
			}

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
