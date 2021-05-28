<?php 

  /**
  * @file
  * Contains \Drupal\import_methodologies\Controller\ImportMethodologiesController
  */

  namespace Drupal\import_methodologies\Controller;
  use \Drupal\Core\Database\Database;
  use \Drupal\node\Entity\Node;

  class ImportMethodologiesController {
    public function getMethodologies() {
      Database::setActiveConnection('methodology');
			$database = Database::getConnection();

      $methodologies = array();
			
      $result = $database->query(
        "SELECT presentation_name, brief_description, version
        FROM methodologies"
			);

			while ($row = $result->fetchAssoc()) {
				array_push($methodologies, $row);
			}
			
			Database::setActiveConnection('default');
			
			return $methodologies;
    }

    public function createMethodologies($methodologies) {
      foreach($methodologies as $methodology) {
        if ($methodology['presentation_name'] == 'OpenUP') {
          $website = 'https://420-gel-hy.github.io/EPF/openup/index.htm';
        }
        else {
          $website = 'https://420-gel-hy.github.io/EPF/ABRD/index.htm';
        }
        $node = Node::create([
          'type'        => 'methodology',
          'title'       => $methodology['presentation_name'],
          'field_description' => $methodology['brief_description'],
          'field_version' => $methodology['version'],
          'field_website' => [
            'value' => '<iframe src="' . $website . '" width="100%" height="800px"></iframe>',
            'format' => 'full_html'
          ]
        ]);
        $node->save();
      }
    }

    public function test() {
			$methodologies = $this->getMethodologies();
      $this->createMethodologies($methodologies);
      return array(
				'#theme' => 'createMethodologies',
				'#methodologies' => $methodologies,
			);
		}
  }