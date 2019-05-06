<?php

require 'CIG_Importer.php';

class CollectTouristObjects {
  private function get_category_mappings() {
    return array(
      'gospodarka i nauka ekologia' => 'C030',
      'gospodarka i nauka ekonomia' => 'C030',
      'gospodarka i nauka konferencja' => 'C030',
      'gospodarka i nauka targi' => 'C030',
      'gospodarka i nauka technika' => 'C030',
      'inne inne' => 'C030',
      'inne kulinarna' => 'C027',
      'inne zdrowie' => 'C027',
      'kultura dla dzieci' => 'C025',
      'kultura edukacja' => 'C025',
      'kultura film' => 'C025',
      'kultura fotografia' => 'C025',
      'kultura historia' => 'C025',
      'kultura inne' => 'C025',
      'kultura jubileusz' => 'C025',
      'kultura kabaret' => 'C025',
      'kultura koncert' => 'C028',
      'kultura konkurs' => 'C025',
      'kultura literatura' => 'C025',
      'kultura ludowa' => 'C024',
      'kultura malarstwo' => 'C025',
      'kultura muzyka' => 'C025',
      'kultura opera' => 'C025',
      'kultura religia' => 'C025',
      'kultura rozrywka' => 'C025',
      'kultura spotkanie' => 'C025',
      'kultura taniec' => 'C025',
      'kultura teatr' => 'C025',
      'kultura warsztaty' => 'C025',
      'kultura wernisaż' => 'C029',
      'kultura widowisko' => 'C025',
      'kultura wykład' => 'C025',
      'kultura wystawa' => 'C029',
      'motoryzacja inne' => 'C026',
      'motoryzacja rajd' => 'C026',
      'motoryzacja wyścigi' => 'C026',
      'sport badminton' => 'C026',
      'sport biegi' => 'C026',
      'sport dla dzieci' => 'C026',
      'sport gry planszowe' => 'C026',
      'sport hokej' => 'C026',
      'sport inne' => 'C026',
      'sport kajaki' => 'C026',
      'sport karciane' => 'C026',
      'sport kolarstwo' => 'C026',
      'sport koszykówka' => 'C026',
      'sport lekkoatletyka' => 'C026',
      'sport łyżwiarstwo' => 'C026',
      'sport narty' => 'C026',
      'sport niepełnosprawni' => 'C026',
      'sport piłka nożna' => 'C026',
      'sport piłka ręczna' => 'C026',
      'sport piłka siatkowa' => 'C026',
      'sport pływanie' => 'C026',
      'sport samolotowy' => 'C026',
      'sport snowboard' => 'C026',
      'sport strzelectwo sportowe' => 'C026',
      'sport szachy' => 'C026',
      'sport sztuki walki' => 'C026',
      'sport tenis' => 'C026',
      'sport tenis stołowy' => 'C026',
      'sport turystyka i rekreacja' => 'C026',
      'sport żeglarstwo' => 'C026',
    );
  }

  private function decode_restrictions($search_condidtions) {
    $allForDistributionChannel = false;
    $searchAttributeAnd = array();
    $searchCategoryAnd = array();
    $objectIdentifier = array();
    $date_from = null;
    $date_to = null;

    $sc = $search_condidtions;

    if (property_exists($sc, 'allForDistributionChannel')) {
      $allForDistributionChannel = $sc->allForDistributionChannel;
    }

    if ($allForDistributionChannel === false) {
      if (property_exists($sc, 'searchAttributeAnd')) {
        if (property_exists($sc->searchAttributeAnd, 'attributeValue')) {
          if (false === is_array($sc->searchAttributeAnd->attributeValue)) {
            $sc->searchAttributeAnd->attributeValue = array($sc->searchAttributeAnd->attributeValue);
          }
          foreach ($sc->searchAttributeAnd->attributeValue as $attribute) {
            $searchAttributeAnd[$attribute->attributeCode] = $attribute->valueToSearch;
          }
        }
      }

      if (property_exists($sc, 'searchCategoryAnd')) {
        if (property_exists($sc->searchCategoryAnd, 'categoryCode')) {
          if (false === is_array($sc->searchCategoryAnd->categoryCode)) {
            $sc->searchCategoryAnd->categoryCode = array($sc->searchCategoryAnd->categoryCode);
          }
          $searchCategoryAnd = $sc->searchCategoryAnd->categoryCode;
        }
      }

      if (property_exists($sc, 'objectIdentifier')) {
        if (property_exists($sc->objectIdentifier, 'identifierRIT')) {
          if (false === is_array($sc->objectIdentifier->identifierRIT)) {
            $objectIdentifier = array($sc->objectIdentifier->identifierRIT);
          } else {
            $objectIdentifier = $sc->objectIdentifier->identifierRIT;
          }
        } else if (property_exists($sc->objectIdentifier, 'identifierSZ')) {
          if (false === is_array($sc->objectIdentifier->identifierSZ)) {
            $ids = array($sc->objectIdentifier->identifierSZ);
          } else {
            $ids = $sc->objectIdentifier->identifierSZ;
          }
          foreach ($ids as $id) {
            if (false === property_exists($id, 'artificialIdentifier')) {
              $id->artificialIdentifier = null;
            }
            if (false === property_exists($id, 'databaseTable')) {
              $id->databaseTable = null;
            }
            if (false === property_exists($id, 'concatenationOfField')) {
              $id->concatenationOfField = null;
            }
            $objectIdentifier[] = array(
              'type'  => $id->identifierType,
              'id'    => $id->artificialIdentifier,
              'table' => $id->databaseTable,
              'concat'=> $id->concatenationOfField,
            );
          }
        }
      }

      if (property_exists($sc, 'lastModifiedRange')) {
        if (property_exists($sc->lastModifiedRange, 'dateFrom')) {
          $date_from = $sc->lastModifiedRange->dateFrom;
        }
        if (property_exists($sc->lastModifiedRange, 'dateTo')) {
          $date_to = $sc->lastModifiedRange->dateTo;
        }
      }
    }

    $restrictions = array(
      'ids' => $objectIdentifier,
      'attributes' => $searchAttributeAnd,
      'categories' => $searchCategoryAnd,
      'date_from' => $date_from,
      'date_to' => $date_to,
    );

    return $restrictions;
  }

  public function searchTouristObjects($request) {
    $sc = $request->searchCondition;
    $restrictions = $this->decode_restrictions($sc);
    $language = $sc->language;

    $importer = new CIG_Importer();
    $xml = $importer->get_xml();
    $happenings = new SimpleXMLElement($xml);

    $response = new \stdClass;
    $response->status = 'OK';
    $response->info = '';

    $response->touristObject = array();

    $count = 0;
    $category_mappings = $this->get_category_mappings();

    foreach ($happenings as $happening) {
      if (empty($restrictions['ids']) === false && in_array($happening['happeningId'], $restrictions['ids']) === false) {
        continue;
      }
      if ($restrictions['date_from'] !== null && strtotime($happening['modified']) < strtotime($restrictions['date_from'])) {
        continue;
      }
      if ($restrictions['date_to'] !== null && strtotime($happening['modified']) > strtotime($restrictions['date_to'])) {
        continue;
      }

      $touristObject = new \stdClass;
      $touristObject->touristObjectIdentifierRIT = new \stdClass;
      $touristObject->touristObjectIdentifierRIT->identifierRIT = $happening['happeningId'];
      $touristObject->touristObjectIdentifierRIT->lastModified = date("Y-d-m", strtotime($happening['modified']));

      $touristObject->categories = array();

      $codes = array();
      foreach ($category_mappings as $coigdzie_category => $rit_category) {
        if ($happening->category == $coigdzie_category) {
          $codes[] = $rit_category;
        }
      }

      if (count($codes) === 0) {
        die('Unknown category ' . $happening->category);
      }

      foreach ($codes as $code) {
        $category = new \stdClass;
        $category->code = $code;
        $touristObject->categories[] = $category;
      }

      $touristObject->attributes = array();

      $attributes = array();
      $attributes['A001'] = $happening->name;
      $attributes['A004'] = '<p>' . nl2br($happening->description) . '</p>';

      $attributes['A009'] = strtoupper($happening->venue->venueVoivodeship);
      $attributes['A010'] = $happening->venue->venueDistrict;
      $attributes['A011'] = $happening->venue->venueCommune;
      $attributes['A012'] = $happening->venue->venueLocality;

      $attributes['A014'] = $happening->venue->venueStreet;
      $attributes['A015'] = $happening->venue->venueHouseNo;
      $attributes['A017'] = $happening->venue->venuePostCode;

      $attributes['A018'] = $happening->venue->venueGeom->lat . ',' . $happening->venue->venueGeom->lon;

      $attributes['A065'] = $happening->happeningUrl;
      $attributes['A069'] = $happening->price;

      $attributes['A105'] = $happening->startDate;
      $attributes['A106'] = $happening->endDate;

      if ($happening->venue->venueAttributes->carParkAvailable == '1') {
        $attributes['A129'] = 'parking';
      }

      if ($happening->venue->venueAttributes->disabledAccess == '1') {
        $attributes['A130'] = 'Udogodnienia dla niepełnosprawnych';
      }

      //if ($happening->venue->venueAttributes->foodAndDrinkAvailable == '1') {
        // brak odpowiednika w RIT
      //}
      if (isset($happening->organizer)) {
        $attributes['A024'] = $happening->organizer->organizerName;
        $attributes['A025'] = strtoupper($happening->organizer->organizerVoivodeship);
        $attributes['A026'] = $happening->organizer->organizerDistrict;
        $attributes['A027'] = $happening->organizer->organizerCommune;
        $attributes['A028'] = $happening->organizer->organizerLocality;
        $attributes['A030'] = $happening->organizer->organizerStreet;
        $attributes['A031'] = $happening->organizer->organizerHouseNo;
        $attributes['A033'] = $happening->organizer->organizerPostCode;
        $attributes['A034'] = $happening->organizer->organizerTelephone;
        $attributes['A039'] = $happening->organizer->organizerEmail;
      }


      foreach ($attributes as $code => $values) {
        $attribute = new \stdClass;
        $attribute->code = $code;
        $attribute->attrVals = new \stdClass;
        $attribute->attrVals->language = $language;
        $attribute->attrVals->value = explode('|', $values);
        $touristObject->attributes[] = $attribute;
      }

      $touristObject->binaryDocuments = new \stdClass;
      $touristObject->binaryDocuments->documentURL = array();

      $images = array();

      if (isset($happening->imageUrl)) {
        $images[] = $happening->imageUrl;
      }

      if (isset($happening->venue->venueImageUrl)) {
        $images[] = $happening->venue->venueImageUrl;
      }

      foreach ($images as $image) {
        $documentURL = new \stdClass;
        $documentURL->fileType = 'image';
        $documentURL->URL = $happening->imageUrl;
        $documentURL->fileName = basename($documentURL->URL);
        $touristObject->binaryDocuments->documentURL[] = $documentURL;
      }

      $response->touristObject[] = $touristObject;
      $count++;
    }

    $response->info = 'Znaleziono '. $count .' obiektów';

    return $response;
  }

}

$webservice = 'CollectTouristObjects';
require 'server.php';
