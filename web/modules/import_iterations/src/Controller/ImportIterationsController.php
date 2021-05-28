<?php 

  /**
  * @file
  * Contains \Drupal\import_iterations\Controller\ImportIterationsController
  */

  namespace Drupal\import_iterations\Controller;
  use \Drupal\Core\Database\Database;
  use \Drupal\node\Entity\Node;

  class ImportIterationsController {
    public function getIterations() {
      Database::setActiveConnection('methodology');
			$database = Database::getConnection();

      $iterations = array();
			
      $result = $database->query(
        "SELECT iterations.presentation_name as iteration_name, iterations.brief_description, phases.presentation_name as phase_name
        FROM iterations JOIN phases ON phases.guid=iterations.phase"
			);

			while ($row = $result->fetchAssoc()) {
				array_push($iterations, $row);
			}
			
			Database::setActiveConnection('default');
			
			return $iterations;
    }

    public function createIterations($iterations) {
      foreach($iterations as $iteration) {
        $phase = \Drupal::entityTypeManager()
          ->getStorage('node')
          ->loadByProperties([
            'title' => $iteration['phase_name'], 
            'type' => 'phase'
          ]);

        $node = Node::create([
          'type'        => 'iteration',
          'title'       => $iteration['iteration_name'],
          'field_description' => $iteration['brief_description'],
          'field_phase' => $phase
        ]);
        $node->save();
      }
    }

    public function test() {
			$iterations = $this->getIterations();
      $this->createIterations($iterations);
      return array(
				'#theme' => 'createIterations',
				'#iterations' => $iterations,
			);
		}
  }