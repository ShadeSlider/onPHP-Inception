<?php

final class PrimitiveListOfForms extends PrimitiveForm
{
    protected $value = array();
    
    /**
     * @var Form
     */
    protected $form;
    
    /**
     * @param string $name
     * @return PrimitiveListOfForms
     */
    public static function create($name)
    {
        return new self($name);
    }

    /**
     * @return PrimitiveListOfForms
    **/
    public function clean()
    {
        parent::clean();

        $this->value = array();

        return $this;
    }
    
    public function ofForm(Form $form)
    {
        $this->form = $form;
        return $this;
    }

    public function setComposite($composite = true)
    {
        throw new UnsupportedMethodException(
            'composition is not supported for lists'
        );
    }

    public function getInnerErrors()
    {
        $result = array();

        foreach ($this->getValue() as $id => $form) {
            if ($errors = $form->getInnerErrors())
                $result[$id] = $errors;
        }

        return $result;
    }    
    
    public function import($scope)
    {
        if (!$this->form)
            throw new WrongStateException(
                "no form defined for PrimitiveFormsList '{$this->name}'"
            );

        if (!BasePrimitive::import($scope))
            return null;

        if (!is_array($scope[$this->name]))
            return false;

        $error = false;

        $this->value = array();



        foreach ($scope[$this->name] as $id => $value) {
            $form = $this->makeForm();
            
            $this->value[$id] = $form->import($value);

            if ($this->value[$id]->getErrors())
                $error = true;
        }

        return !$error;
    }

    public function importValue($value)
    {
        if ($value !== null) {

	        if ($value instanceof OneToManyLinked) {
		        $formList = array();
				foreach ($value->getList() as $object) {
					$objectForm = $object->proto()->makeForm();
					FormUtils::object2form($object, $objectForm);

					$formList[] = $objectForm;
				}

		        $value = $formList;
	        }

	        Assert::isArray($value);
        }
        else
            return null;

        $result = true;

        $resultValue = array();

        foreach ($value as $id => $form) {
            Assert::isInstance($form, 'Form');

            $resultValue[$id] = $form;

            if ($form->getErrors())
                $result = false;
        }

        $this->value = $resultValue;

        return $result;
    }

    public function exportValue()
    {
        if (!$this->isImported())
            return null;

        $result = array();

        foreach ($this->value as $id => $form) {
            $result[$id] = $form->export();
        }

        return $result;
    }    
    
    protected function makeForm()
    {
        $form = Form::create();
        
        foreach($this->form->getPrimitiveList() as $primitive) {
            $clonedPrimitive = clone $primitive;
            $form->add($clonedPrimitive);
        }
        return $form;
    }

	/**
	 * @param $value
	 * @throws WrongArgumentException
	 * @return PrimitiveForm
	 **/
	public function setValue($value)
	{
		Assert::isArray($value);

		$this->value = $value;

		return $this;
	}

	/**
	 * @return \Form
	 */
	public function getForm()
	{
		return $this->form;
	}
}