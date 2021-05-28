<?php 

  /**
  * @file
  * Contains \Drupal\import_activities\Controller\ImportActivitiesController
  */

  namespace Drupal\import_activities\Controller;
  use \Drupal\Core\Database\Database;
  use \Drupal\node\Entity\Node;

  class ImportActivitiesController {
    public function getActivities() {
      Database::setActiveConnection('methodology');
      $database = Database::getConnection();

      $activities = array();
      
      $result = $database->query(
        "SELECT activities.presentation_name as activity_name, activities.brief_description, iterations.presentation_name as iteration_name, phases.presentation_name as phase_name
        FROM activities JOIN iteration_activities ON iteration_activities.activity=activities.guid
        JOIN iterations ON iteration_activities.iteration=iterations.guid
        JOIN phases ON iterations.phase=phases.guid"
      );

      while ($row = $result->fetchAssoc()) {
        array_push($activities, $row);
      }

      $result = $database->query(
        "SELECT activities.presentation_name as activity_name, activities.brief_description, phases.presentation_name as phase_name
        FROM activities JOIN phase_activities ON phase_activities.activity=activities.guid
        JOIN phases ON phase_activities.phase=phases.guid"
      );

      while ($row = $result->fetchAssoc()) {
        array_push($activities, $row);
      }
      
      Database::setActiveConnection('default');
      
      return $activities;
    }

    public function createActivities($activities) {
      foreach($activities as $activity) {
        $iteration = '';
        if ($activity['iteration_name'] != "") {
          $iteration = \Drupal::entityTypeManager()
          ->getStorage('node')
          ->loadByProperties([
            'title' => $activity['iteration_name'], 
            'type' => 'iteration'
          ]);
        }

        $phase = \Drupal::entityTypeManager()
        ->getStorage('node')
        ->loadByProperties([
          'title' => $activity['phase_name'], 
          'type' => 'phase'
        ]);

        $node = Node::create([
          'type' => 'activity',
          'title' => $activity['activity_name'],
          'field_description' => $activity['brief_description'],
          'field_iteration' => $iteration,
          'field_phase' => $phase
        ]);
        $node->save();
      }
    }

    public function test() {
      $activities = $this->getActivities();
      $this->createActivities($activities);
      return array(
        '#theme' => 'createActivities',
        '#activities' => $activities,
      );
    }
  }