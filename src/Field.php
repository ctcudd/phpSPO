<?php


namespace SharePoint\PHP\Client;

/**
 * Represents a field in a SharePoint list.
 */
class Field extends ClientObject
{
	public function setShowInAll($value){
		$this->setShowInDisplayForm($value);
		$this->setShowInEditForm($value);
		$this->setShowInNewForm($value);
	}
	
    /**
     * Sets the value of the ShowInDisplayForm property for this field.
     * @param $value true to show the field in the form; otherwise false.
     */
    public function setShowInDisplayForm($value){
        $this->setShowInform($value, "display");
    }
    /**
     * Sets the value of the ShowInEditForm property for this field.
     * @param $value true to show the field in the form; otherwise false.
     */
    public function setShowInEditForm($value){
    	$this->setShowInform($value, "edit");
    }
    /**
     * Sets the value of the ShowInNewForm property for this field.
     * @param $value true to show the field in the form; otherwise false.
     */
    public function setShowInNewForm($value){
    	$this->setShowInform($value, "new");
    }
    
    private function setShowInForm($value, $form){
    	$url = $this->getUrl() . "/setshowin${form}form(" . var_export($value, true) . ")";
    	$qry = new ClientQuery($url,ClientActionType::Update);
    	$this->getContext()->addQuery($qry);
    }
    
    /**
     * Updates field
     * @param $fieldUpdateInformation
     */
    public function update($fieldUpdateInformation){
    	$qry = new ClientQuery($this->getUrl(),ClientActionType::Update,$fieldUpdateInformation);
    	$this->getContext()->addQuery($qry,$this);
    }
    
    public function delete(){
    	//TODO
    }
}