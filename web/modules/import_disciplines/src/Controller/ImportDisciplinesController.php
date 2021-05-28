<?php 

  /**
  * @file
  * Contains \Drupal\import_disciplines\Controller\ImportDisciplinesController
  */

  namespace Drupal\import_disciplines\Controller;
  use \Drupal\Core\Database\Database;
  use Drupal\taxonomy\Entity\Vocabulary;
  use Drupal\taxonomy\Entity\Term;

  class ImportDisciplinesController {
    public function getDisciplines() {
      Database::setActiveConnection('methodology');
			$database = Database::getConnection();

      $disciplines = array();
			
      $result = $database->query(
        "SELECT disciplines.name, disciplines.presentation_name as discipline_name, disciplines.brief_description, methodologies.presentation_name as methodology_name
        FROM disciplines JOIN methodologies ON methodologies.guid=disciplines.methodology"
			);

			while ($row = $result->fetchAssoc()) {
        if ($row['name'] != 'to_delete' && $row['name'] != 'to_delete_2') {
          $methodology = \Drupal::entityTypeManager()
          ->getStorage('node')
          ->loadByProperties([
            'title' => $row['methodology_name'], 
            'type' => 'methodology'
          ]);

          $discipline = array(
            'tid' => $row['name'],
            'name' => $row['discipline_name'], 
            'description' => $row['brief_description'],
            'vid' => 'disciplines',
            'field_methodology' => $methodology
          );
          array_push($disciplines, $discipline);
        }
			}
			
			Database::setActiveConnection();

      return $disciplines;
    }

    public function createDisciplines($disciplines) {
      foreach($disciplines as $discipline) {
        if (!Term::load($discipline['tid'])) {
          $drupal_discipline = Term::create($discipline);
          $drupal_discipline->save();
        }
      }
    }

    public function test() {
			$disciplines = $this->getDisciplines();
      $this->createDisciplines($disciplines);
      return array(
				'#theme' => 'createDisciplines',
				'#disciplines' => $disciplines,
			);
		}
  }