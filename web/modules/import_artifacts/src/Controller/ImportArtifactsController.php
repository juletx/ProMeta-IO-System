<?php 

  /**
  * @file
  * Contains \Drupal\import_artifacts\Controller\ImportArtifactsController
  */

  namespace Drupal\import_artifacts\Controller;
  use \Drupal\Core\Database\Database;
  use \Drupal\node\Entity\Node;
  use Drupal\taxonomy\Entity\Term;

  class ImportArtifactsController {
    public function getArtifacts() {
      Database::setActiveConnection('methodology');
			$database = Database::getConnection();

      $artifacts = array();
			
      $result = $database->query(
        "SELECT artifacts.presentation_name as artifact_name, artifacts.brief_description, 
        domains.presentation_name as domain_name, roles.presentation_name as role_name
        FROM artifacts JOIN role_artifacts ON artifacts.guid=role_artifacts.artifact
        JOIN roles ON roles.guid=role_artifacts.role
        JOIN domains ON domains.guid=artifacts.domain"
			);

			while ($row = $result->fetchAssoc()) {
				array_push($artifacts, $row);
			}
			
			Database::setActiveConnection('default');
			
			return $artifacts;
    }

    public function createArtifacts($artifacts) {
      foreach($artifacts as $artifact) {
        $role = \Drupal::entityTypeManager()
        ->getStorage('node')
        ->loadByProperties([
          'title' => $artifact['role_name'], 
          'type' => 'role'
        ]);

        $domain = \Drupal::entityTypeManager()
        ->getStorage('taxonomy_term')
        ->loadByProperties([
          'name' => $artifact['domain_name'], 
          'vid' => 'domains'
        ]);

        $node = Node::create([
          'type' => 'artifact',
          'title' => $artifact['artifact_name'],
          'field_description' => $artifact['brief_description'],
          'field_role_desc' => $role,
          'field_domain' => $domain
        ]);
        $node->save();
      }
    }

    public function test() {
			$artifacts = $this->getArtifacts();
      $this->createArtifacts($artifacts);
      return array(
				'#theme' => 'createArtifacts',
				'#artifacts' => $artifacts,
			);
		}
  }