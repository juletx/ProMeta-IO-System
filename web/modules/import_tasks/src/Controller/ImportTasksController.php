<?php 

  /**
  * @file
  * Contains \Drupal\import_tasks\Controller\ImportTasksController
  */

  namespace Drupal\import_tasks\Controller;
  use \Drupal\Core\Database\Database;
  use \Drupal\node\Entity\Node;
  use Drupal\taxonomy\Entity\Term;

  class ImportTasksController {
    public function getTasks() {
      Database::setActiveConnection('methodology');
			$database = Database::getConnection();

      $tasks = array();
			
      $result = $database->query(
        "SELECT tasks.presentation_name as task_name, tasks.brief_description, 
        disciplines.presentation_name as discipline_name, roles.presentation_name as role_name, 
        activities.presentation_name as activity_name, iterations.presentation_name as iteration_name,
        phases.presentation_name as phase_name
        FROM tasks JOIN role_tasks ON tasks.guid=role_tasks.task
        JOIN roles ON roles.guid=role_tasks.role
        JOIN disciplines ON disciplines.guid=tasks.discipline
        JOIN activity_tasks ON tasks.guid=activity_tasks.task
        JOIN activities ON activities.guid=activity_tasks.activity
        JOIN iteration_activities ON iteration_activities.activity=activities.guid
        JOIN iterations ON iteration_activities.iteration=iterations.guid
        JOIN phases ON iterations.phase=phases.guid"
			);

			while ($row = $result->fetchAssoc()) {
				array_push($tasks, $row);
			}

      $result = $database->query(
        "SELECT tasks.presentation_name as task_name, tasks.brief_description, 
        disciplines.presentation_name as discipline_name, roles.presentation_name as role_name, 
        activities.presentation_name as activity_name, phases.presentation_name as phase_name
        FROM tasks JOIN role_tasks ON tasks.guid=role_tasks.task
        JOIN roles ON roles.guid=role_tasks.role
        JOIN disciplines ON disciplines.guid=tasks.discipline
        JOIN activity_tasks ON tasks.guid=activity_tasks.task
        JOIN activities ON activities.guid=activity_tasks.activity
        JOIN phase_activities ON phase_activities.activity=activities.guid
        JOIN phases ON phase_activities.phase=phases.guid"
			);

      while ($row = $result->fetchAssoc()) {
				array_push($tasks, $row);
			}

      $result = $database->query(
        "SELECT tasks.presentation_name as task_name, tasks.brief_description, 
        disciplines.presentation_name as discipline_name, roles.presentation_name as role_name, 
        iterations.presentation_name as iteration_name, phases.presentation_name as phase_name
        FROM tasks JOIN role_tasks ON tasks.guid=role_tasks.task
        JOIN roles ON roles.guid=role_tasks.role
        JOIN disciplines ON disciplines.guid=tasks.discipline
        JOIN iteration_tasks ON tasks.guid=iteration_tasks.task
        JOIN iterations ON iteration_tasks.iteration=iterations.guid
        JOIN phases ON iterations.phase=phases.guid"
			);

      while ($row = $result->fetchAssoc()) {
				array_push($tasks, $row);
			}

      $result = $database->query(
        "SELECT tasks.presentation_name as task_name, tasks.brief_description, 
        disciplines.presentation_name as discipline_name, roles.presentation_name as role_name, 
        phases.presentation_name as phase_name
        FROM tasks JOIN role_tasks ON tasks.guid=role_tasks.task
        JOIN roles ON roles.guid=role_tasks.role
        JOIN disciplines ON disciplines.guid=tasks.discipline
        JOIN phase_tasks ON tasks.guid=phase_tasks.task
        JOIN phases ON phase_tasks.phase=phases.guid"
			);

      while ($row = $result->fetchAssoc()) {
				array_push($tasks, $row);
			}
			
			Database::setActiveConnection('default');
			
			return $tasks;
    }

    public function createTasks($tasks) {
      foreach($tasks as $task) {
        $role = \Drupal::entityTypeManager()
        ->getStorage('node')
        ->loadByProperties([
          'title' => $task['role_name'], 
          'type' => 'role'
        ]);

        $discipline = \Drupal::entityTypeManager()
        ->getStorage('taxonomy_term')
        ->loadByProperties([
          'name' => $task['discipline_name'], 
          'vid' => 'disciplines'
        ]);

        $activity = '';
        if ($task['activity_name'] != "") {
          $activity = \Drupal::entityTypeManager()
          ->getStorage('node')
          ->loadByProperties([
            'title' => $task['activity_name'], 
            'type' => 'activity'
          ]);
        }

        $iteration = '';
        if ($task['iteration_name'] != "") {
          $iteration = \Drupal::entityTypeManager()
          ->getStorage('node')
          ->loadByProperties([
            'title' => $task['iteration_name'], 
            'type' => 'iteration'
          ]);
        }

        $phase = '';
        if ($task['phase_name'] != "") {
          $phase = \Drupal::entityTypeManager()
          ->getStorage('node')
          ->loadByProperties([
            'title' => $task['phase_name'], 
            'type' => 'phase'
          ]);
        }

        $input_artifact = '';
        if ($task['input_artifact_name'] != "") {
          $input_artifact = \Drupal::entityTypeManager()
          ->getStorage('node')
          ->loadByProperties([
            'title' => $task['input_artifact_name'], 
            'type' => 'artifact'
          ]);
        }

        $output_artifact = '';
        if ($task['output_artifact_name'] != "") {
          $output_artifact = \Drupal::entityTypeManager()
          ->getStorage('node')
          ->loadByProperties([
            'title' => $task['output_artifact_name'], 
            'type' => 'artifact'
          ]);
        }

        $node = Node::create([
          'type' => 'task',
          'title' => $task['task_name'],
          'field_description' => $task['brief_description'],
          'field_role_desc' => $role,
          'field_discipline' => $discipline,
          'field_activity' => $activity,
          'field_phase' => $phase,
          'field_iteration' => $iteration,
          'field_input_artifact' => $input_artifact,
          'field_output_artifact' => $output_artifact
        ]);
        $node->save();
      }
    }

    public function test() {
			$tasks = $this->getTasks();
      $this->createTasks($tasks);
      return array(
				'#theme' => 'createTasks',
				'#tasks' => $tasks,
			);
		}
  }