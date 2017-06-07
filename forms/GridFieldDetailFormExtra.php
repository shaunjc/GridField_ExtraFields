<?php
/**
 * Automatically add in ManyMany extra fields when editing a relational object.
 * 
 * You can either replace the default GridFieldDetailForm with a new
 * GridFieldDetailFormExtra, or update protected $itemRequestClass
 * as illustrated in the default usage below.
 * 
 * Default Usage:
 * $gridField
 *  ->getConfig()
 *  ->getComponentByType( 'GridFieldDetailForm' )
 *  ->setItemRequestClass( 'GridFieldDetailFormExtra_ItemRequest' );
 */
class GridFieldDetailFormExtra extends GridFieldDetailForm {
    
}

class GridFieldDetailFormExtra_ItemRequest extends GridFieldDetailForm_ItemRequest {
    /** @var array **/
    public static $allowed_actions = array(
        'ItemEditForm'
    );
    
    /**
     * Adds Extra fields to the bottom of the Main Tab,
     * used for updating many_many_extraFields.
     * 
     * @return Form
     */
    public function ItemEditForm() {
        // Collect Form, Fields, and ManyManyList.
        $form = parent::ItemEditForm();
		$fields = $form->Fields();
		$list = $this->gridField->getList();
        
        // Can also test to see if $list is a ManyManyList.
		if( $list->hasMethod( 'getExtraFields' ) && ( $extraFields = $list->getExtraFields() ) ) {
            // Get the class name and/or Foreign Object if available.
			$key = $list->getForeignKey();
            $class = preg_replace( '/ID$/', '', $key );
			$id = $list->getForeignID();
            $foreignObject = ( class_exists( $class ) && is_a( $class, 'DataObject', true ) ) ? $class::get()->byID( $id ) : null;
            
            foreach ( $extraFields as $fieldName => $fieldSpec ) {
                $title = "$fieldName (" . ( $foreignObject && $foreignObject->exists() && $foreignObject->Title ? $foreignObject->Title : "$class:$id" ) . ")";
                $fields->addFieldToTab( 'Root.Main',
                    // Create each field using standard scaffolding techniques.
                    Object::create_from_string( $fieldSpec, "ManyMany[$fieldName]" )->scaffoldFormField( $title )
                );
            }
            
            // Prevent NumericField localisation issues by converting all numeric values to strings.
			$extraData = array_map( function( $data ){
			    return "$data";
			}, (array) $list->getExtraData( '', $this->record->ID ) );
            // Re-load data from form.
			$form->loadDataFrom( array('ManyMany' => $extraData ) );
		}
        
        return $form;
    }
}
