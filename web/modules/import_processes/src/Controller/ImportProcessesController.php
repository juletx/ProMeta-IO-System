<?php 

  /**
  * @file
  * Contains \Drupal\import_processes\Controller\ImportProcessesController
  */

  namespace Drupal\import_processes\Controller;
  use \Drupal\Core\Database\Database;
  use \Drupal\node\Entity\Node;

  class ImportProcessesController {
    public function getProcesses() {
      Database::setActiveConnection('methodology');
			$database = Database::getConnection();

      $processes = array();
			
      $result = $database->query(
        "SELECT processes.presentation_name as process_name, processes.brief_description, methodologies.presentation_name as methodology_name
        FROM processes JOIN methodologies ON methodologies.guid=processes.methodology"
			);

			while ($row = $result->fetchAssoc()) {
				array_push($processes, $row);
			}
			
			Database::setActiveConnection('default');
			
			return $processes;
    }

    public function createProcesses($processes) {
      foreach($processes as $process) {
        $methodology = \Drupal::entityTypeManager()
          ->getStorage('node')
          ->loadByProperties([
            'title' => $process['methodology_name'], 
            'type' => 'methodology'
          ]);

        $node = Node::create([
          'type'        => 'process',
          'title'       => $process['process_name'],
          'field_description' => $process['brief_description'],
          'field_methodology' => $methodology
        ]);
        $node->save();
      }
    }

    public function test() {
			$processes = $this->getProcesses();
      $this->createProcesses($processes);
      return array(
				'#theme' => 'createProcesses',
				'#processes' => $processes,
			);
		}
  }