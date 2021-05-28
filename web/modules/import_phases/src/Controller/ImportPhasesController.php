<?php 

  /**
  * @file
  * Contains \Drupal\import_phases\Controller\ImportPhasesController
  */

  namespace Drupal\import_phases\Controller;
  use \Drupal\Core\Database\Database;
  use \Drupal\node\Entity\Node;

  class ImportPhasesController {
    public function getPhases() {
      Database::setActiveConnection('methodology');
			$database = Database::getConnection();

      $phases = array();
			
      $result = $database->query(
        "SELECT phases.presentation_name as phase_name, phases.brief_description, processes.presentation_name as process_name
        FROM phases JOIN processes ON processes.guid=phases.process"
			);

			while ($row = $result->fetchAssoc()) {
				array_push($phases, $row);
			}
			
			Database::setActiveConnection('default');
			
			return $phases;
    }

    public function createPhases($phases) {
      foreach($phases as $phase) {
        $process = \Drupal::entityTypeManager()
          ->getStorage('node')
          ->loadByProperties([
            'title' => $phase['process_name'], 
            'type' => 'process'
          ]);

        $node = Node::create([
          'type'        => 'phase',
          'title'       => $phase['phase_name'],
          'field_description' => $phase['brief_description'],
          'field_process' => $process
        ]);
        $node->save();
      }
    }

    public function test() {
			$phases = $this->getPhases();
      $this->createPhases($phases);
      return array(
				'#theme' => 'createPhases',
				'#phases' => $phases,
			);
		}
  }