<?php 

  /**
  * @file
  * Contains \Drupal\import_roles\Controller\ImportRolesController
  */

  namespace Drupal\import_roles\Controller;
  use \Drupal\Core\Database\Database;
  use \Drupal\user\Entity\Role;
  use \Drupal\node\Entity\Node;

  class ImportRolesController {
    public function getRoles() {
      Database::setActiveConnection('methodology');
			$database = Database::getConnection();

      $roles = array();
			
      $result = $database->query(
        "SELECT roles.name, roles.presentation_name as role_name, roles.brief_description, methodologies.presentation_name as methodology_name
        FROM role_sets JOIN roles ON role_sets.guid=roles.role_set 
        JOIN methodologies ON methodologies.guid=role_sets.methodology"
			);

			while ($row = $result->fetchAssoc()) {
				array_push($roles, $row);
			}
			
			Database::setActiveConnection('default');
			
			return $roles;
    }

    public function createRoles($roles) {
      foreach($roles as $role) {
        $drupal_role = Role::load($role['name']);
        if (!$drupal_role) {
          $drupal_role = Role::create([
            'id' => $role['name'],
            'label' => $role['role_name']
          ]);
          $drupal_role->save();
        }

        $methodology = \Drupal::entityTypeManager()
          ->getStorage('node')
          ->loadByProperties([
            'title' => $role['methodology_name'], 
            'type' => 'methodology'
          ]);

        $node = Node::create([
          'type'        => 'role',
          'title'       => $role['role_name'],
          'field_description' => $role['brief_description'],
          'field_role' => $drupal_role,
          'field_methodology' => $methodology
        ]);
        $node->save();
      }
    }

    public function test() {
			$roles = $this->getRoles();
      $this->createRoles($roles);
      return array(
				'#theme' => 'createRoles',
				'#roles' => $roles,
			);
		}
  }