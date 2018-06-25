<?php

namespace Webmaxsk\MaxImages;

use ReflectionClass;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\DropdownField;
use SilverStripe\AssetAdmin\Forms\UploadField;
use Bummzack\SortableFile\Forms\SortableUploadField;

class ObjectImagesExtension extends DataExtension {

    private static $images = [
        'enabled' => true,
        'count' => 20,
    ];

    private static $db = [
        'Sorter' => 'Enum("SortOrder, Title, Name, ID")'
    ];

    private static $many_many = [
        'Images' => Image::class
    ];

    private static $many_many_extraFields = [
        'Images' => ['SortOrder' => 'Int']
    ];

    private static $owns = [
        'Images'
    ];

    public function updateCMSFields(FieldList $fields) {
        $imagesTab = $fields->findOrMakeTab('Root.Images');

        if ($this->owner->getImagesOption('enabled')) {
            $limit = $this->owner->getImagesOption('count');

            $uploadClass = ($this->owner->Sorter == 'SortOrder') ? SortableUploadField::class : UploadField::class;

            $imageField = $uploadClass::create('Images');
            $imageField->setAllowedMaxFileNumber($limit);
            $imageField->setFolderName('Uploads/' . (new ReflectionClass($this->owner))->getShortName() . '/' . $this->owner->ID);

            if ($limit == 1) {
                $imagesTab->setTitle(_t('Object.IMAGETAB', 'Images'));
                $imageField->setTitle(_t('Object.IMAGEUPLOADLABEL', 'Image'));
            }
            else {
                $imagesTab->setTitle(_t('Object.IMAGESTAB', 'Images'));
                $imageField->setTitle(_t('Object.IMAGESUPLOADLABEL', 'Images'));
                $imageField->setDescription(sprintf(_t('Object.IMAGESUPLOADLIMIT','Images count limit: %s'), $limit));

                if ($this->owner->Sorter == 'SortOrder')
                    $message = _t('Object.IMAGESUPLOADHEADING', '<span style="color: green">Sort images by dragging thumbnail</span>');
                else
                    $message = _t('Object.IMAGESSORTERNOTICE', 'Correct image sorting is visible on frontend only (if Sort by = Title, ID)');

                $fields->addFieldToTab('Root.Images',
                    DropdownField::create('Sorter', _t('Object.IMAGESSORTER', 'Sort images by: '))->setSource($this->owner->dbObject('Sorter')->enumValues())
                        ->setDescription($message)
                );
            }

            $fields->addFieldToTab('Root.Images', $imageField);
        }
        else {
            $fields->removeByName('Sorter');
            $fields->removeByName($imagesTab->Name);
        }
    }

    public function SortedImages() {
        return $this->owner->Images()->Sort($this->owner->Sorter);
    }

    public function MainImage() {
        return $this->owner->Images()->Sort($this->owner->Sorter)->limit(1)->First();
    }

    public function getImagesOption($key)
    {
        $settings = $this->getImagesOptions();
        $value = null;

        if (isset($settings[$key])) {
            $value = $settings[$key];
        }

        // To allow other extensions to customise this option
        if ($this->owner) {
            $this->owner->extend('updateImagesOption', $key, $value);
        }

        return $value;
    }

    public function getImagesOptions()
    {
        $settings = [];

        if ($this->owner) {
            $settings = $this->owner->config()->get('images');
        } else {
            $settings = Config::inst()->get(__CLASS__, 'images');
        }

        return $settings;
    }
}

// EOF
