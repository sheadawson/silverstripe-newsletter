<?php
/**
 * @package  newsletter
 */

/**
 * Represents a specific containner of newsletter recipients 
 */
class MailingList extends DataObject {

	/* the database fields */
	static $db = array(
		'Title'					=> "Varchar",
	);

	/* a mailing list could contains many newsletter recipients */
	static $many_many = array(
		'Recipients'			=> "Recipient",
	);

	static $belongs_many_many = array(
		'Newsletters'			=> "Newsletter",
	);

	static $singular_name = 'Mailinglist';

	static $plural_name = 'Mailinglists';

	public function fieldLabels($includelrelations = true) {
		$labels = parent::fieldLabels($includelrelations);
		$labels["Title"] = _t('Newsletter.FieldTitle', "Title");
		return $labels;
	}
	
	function getCMSFields() {
		$fields = new FieldList();
		$fields->push(new TabSet("Root", $mainTab = new Tab("Main")));
		$mainTab->setTitle(_t('SiteTree.TABMAIN', "Main"));

		$fields->addFieldToTab('Root.Main',
			new TextField('Title',_t('NewsletterAdmin.MailingListTitle','Mailing List Title')));

		$gridFieldConfig = GridFieldConfig::create()->addComponents(
			new GridFieldToolbarHeader(),
			new GridFieldSortableHeader(),
			$dataColumns = new GridFieldDataColumns(),
			new GridFieldFilterHeader(),
			new GridFieldDeleteAction(true),
			new GridFieldPaginator(30),
			new GridFieldAddNewButton(),
			new GridFieldDetailForm(),
			new GridFieldEditButton(),
			$autocompelete = new GridFieldAutocompleterWithFilter('before',	array(
					'FirstName',
					'MiddleName',
					'Surname',
					'Email',
				)
			)
		);

		$dataColumns -> setFieldCasting(array(
			"Blacklisted" => "Boolean->Nice",
			"Verified" => "Boolean->Nice",
		));

		$autocompelete->filters = array(
			"Blacklisted" => false,
		);

		$recipientsGrid = GridField::create(
			'Recipients',
			_t('NewsletterAdmin.Recipients', 'Mailing list recipients'),
			$this->Recipients(),
			$gridFieldConfig
		);


		$fields->addFieldToTab('Root.Main',new FieldGroup($recipientsGrid));
		$this->extend("updateCMSFields", $fields);		

		return $fields;
	}
}