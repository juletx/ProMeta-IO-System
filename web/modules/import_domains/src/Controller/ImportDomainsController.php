<?php 

  /**
  * @file
  * Contains \Drupal\import_domains\Controller\ImportDomainsController
  */

  namespace Drupal\import_domains\Controller;
  use \Drupal\Core\Database\Database;
  use Drupal\taxonomy\Entity\Vocabulary;
  use Drupal\taxonomy\Entity\Term;

  class ImportDomainsController {
    public function getDomains() {
      Database::setActiveConnection('methodology');
			$database = Database::getConnection();

      $domains = array();
			
      $result = $database->query(
        "SELECT domains.name, domains.presentation_name as domain_name, domains.brief_description, methodologies.presentation_name as methodology_name
        FROM domains JOIN methodologies ON methodologies.guid=domains.methodology"
			);

			while ($row = $result->fetchAssoc()) {
        $methodology = \Drupal::entityTypeManager()
          ->getStorage('node')
          ->loadByProperties([
            'title' => $row['methodology_name'], 
            'type' => 'methodology'
          ]);

        $domain = array(
          'tid' => $row['name'],
          'name' => $row['domain_name'], 
          'description' => $row['brief_description'],
          'vid' => 'domains',
          'field_methodology' => $methodology
        );
				array_push($domains, $domain);
			}
			
			Database::setActiveConnection();

      return $domains;
    }

    public function createDomains($domains) {
      foreach($domains as $domain) {
        if (!Term::load($domain['tid'])) {
          $drupal_domain = Term::create($domain);
          $drupal_domain->save();
        }
      }
    }

    public function test() {
			$domains = $this->getDomains();
      $this->createDomains($domains);
      return array(
				'#theme' => 'createDomains',
				'#domains' => $domains,
			);
		}
  }