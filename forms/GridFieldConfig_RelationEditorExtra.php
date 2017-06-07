<?php
/**
 * GridFieldConfig class used to automatically display extra fields
 * when editing relational objects through the ItemEditForm or when
 * adding items via the GridFieldAddExistingAutocompleter.
 * 
 * This can be set as the default Relation Editor by setting the
 * Injector config in the yaml files.
 */
class GridFieldConfig_RelationEditorExtra extends GridFieldConfig_RelationEditor {
    /**
     * @var boolean Default true.
     */
    protected static $automaticAutocompleter = true;
    
    /**
     * @var boolean Default true.
     */
    protected static $automaticItemEditForm = true;
    
	/**
	 * @param int $itemsPerPage - How many items per page should show up
	 */
	public function __construct( $itemsPerPage = null ) {
        // Load default configuration
		parent::__construct( $itemsPerPage );
        
        if ( static::$automaticAutocompleter )
            $this->setAutocompleterExtra();
        
        if ( static::$automaticItemEditForm )
            $this->setItemRequestExtra();
        
        // Secondary update function
		$this->extend( 'updateExtraConfig' );
	}
    
    /**
     * Replace GridFieldAddExistingAutocompleter with
     * similar GridFieldAddExistingAutocompleterExtra
     * 
     * @param string $targetFragment
     * @param array|null $searchFields
     */
    public function setAutocompleterExtra( $targetFragment = 'buttons-before-right', $searchFields = null ) {
        $this->removeComponentsByType( 'GridFieldAddExistingAutocompleter' );
        $this->addComponent( new GridFieldAddExistingAutocompleterExtra( $targetFragment, $searchFields ) );
    }
    
    /**
     * Set Item Request Class
     */
    public function setItemRequestExtra() {
        $this->getComponentByType( 'GridFieldDetailForm' )
             ->setItemRequestClass( 'GridFieldDetailFormExtra_ItemRequest' );
    }
}
