<?php


namespace Strokit\CoreBundle\Listener;


class SluggableListener extends \Gedmo\Sluggable\SluggableListener {

    public function __construct(){
        $this->setTransliterator(array('\Strokit\CoreBundle\Service\Transliterator', 'transliterate'));
    }
}