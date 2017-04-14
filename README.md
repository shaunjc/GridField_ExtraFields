# GridField_ExtraFields
Easily manage many_many_extraFields when adding objects to a ManyManyList via a GridField.

## TODO:
Create matching GridField_FormAction, GridFieldDataColumns, GridFieldConfig,
and/or GridFieldDetailForm to modify extra fields when objects have already
been linked to the Foreign Key, as well as an injector to use the new classes
by default.

## Usage:
Currently, all implementations need to be done manually on a per class basis.

    public function getCMSFields() {
        $fields = parent::getCMSFields();
        
        // Get ManyMany-RelationEditor-GridField from standard scaffold implementation.
        if ( ( $gridField = $fields->fieldByName( 'Root.RelationName.RelationName' ) ) ) {
            // Determine if it has a ManyManyList and collect the extraFields (optional)
            if ( $gridField->getList()->hasMethod( 'getExtraFields' ) && ( $extraFields = array_keys( $gridField->getList()->getExtraFields() ) ) ) {
                $config = $gridField->getConfig();
                // Remove existing implementation of GridFieldAddExistingAutocompleter (optional, but pointless to keep it)
                $config->removeComponentsByType( 'GridFieldAddExistingAutocompleter' );
                // Replace with new GridFieldAddExistingAutocompleterExtra class with same constructor
                $config->addComponent( new \GridFieldAddExistingAutocompleterExtra( 'buttons-before-right' ) );
                // Update Table Columns to display many_many_extraFields values (oprional).
                $config->getComponentByType( 'GridFieldDataColumns' )
                    ->setDisplayFields(
                        singleton( $this-getRelationClass( 'RelationName') )->summaryFields()
                            // You can either use $extraFields to add extra columns
                            + array_combine( $extraFields, $extraFields )
                            // Or you can set your own labels, for instance when $this belongs to the other object
                            + array( 'Value', $this->Title )
                    );
            }
        }
        
        return $fields;
    }
