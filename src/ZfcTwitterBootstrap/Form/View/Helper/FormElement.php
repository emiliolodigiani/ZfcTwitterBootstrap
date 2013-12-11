<?php
/**
 * ZfcTwitterBootstrap
 */

namespace ZfcTwitterBootstrap\Form\View\Helper;

use Zend\Form\ElementInterface;
use Zend\Form\View\Helper\FormElement as ZendFormElement;
use Zend\Form\View\Helper\FormLabel;
use Zend\Form\View\Helper\FormElementErrors;
use Zend\View\Helper\EscapeHtml;

/**
 * Form Element
 */
class FormElement extends ZendFormElement
{
    /**
     * @var \Zend\Form\View\Helper\FormLabel
     */
    protected $labelHelper;

    /**
     * @var \Zend\Form\View\Helper\ZendFormElement
     */
    protected $elementHelper;

    /**
     * @var \Zend\View\Helper\EscapeHtml
     */
    protected $escapeHelper;

    /**
     * @var \Zend\Form\View\Helper\FormElementErrors
     */
    protected $elementErrorHelper;

    /**
     * @var FormDescription
     */
    protected $descriptionHelper;

    /**
     * Group wrapper pattern
     *   %s 1: container class (usually .form-group)
     *   %s 2: id that refers to form control(s)
     *   %s 3: contents (label, controls, errors, description)
     * @var string
     */
    protected $groupWrapper = '<div class="%s" id="form-group-%s">%s</div>';

    /**
     * Wrap controls in column info, etc (i.e. '<div class="col-sm-offset-2 col-sm-10">%s</div>' )
     * previously: '<div class="controls" id="controls-%s">%s%s%s</div>'
     * @var string
     */
    protected $controlWrapper = '';

    /**
     * Set Label Helper
     *
     * @param  Zend\Form\View\Helper\FormLabel $labelHelper
     * @return self
     */
    public function setLabelHelper(FormLabel $labelHelper)
    {
        $labelHelper->setView($this->getView());
        $this->labelHelper = $labelHelper;

        return $this;
    }

    /**
     * Get Label Helper
     *
     * @return \Zend\Form\View\Helper\FormLabel
     */
    public function getLabelHelper()
    {
        if (!$this->labelHelper) {
            $this->setLabelHelper($this->view->plugin('formlabel'));
        }

        return $this->labelHelper;
    }

    /**
     * Set EscapeHtml Helper
     *
     * @param  \Zend\View\Helper\EscapeHtml $escapeHelper
     * @return self
     */
    public function setEscapeHtmlHelper(EscapeHtml $escapeHelper)
    {
        $escapeHelper->setView($this->getView());
        $this->escapeHelper = $escapeHelper;

        return $this;
    }

    /**
     * Get EscapeHtml Helper
     *
     * @return \Zend\View\Helper\EscapeHtml
     */
    public function getEscapeHtmlHelper()
    {
        if (!$this->escapeHelper) {
            $this->setEscapeHtmlHelper($this->view->plugin('escapehtml'));
        }

        return $this->escapeHelper;
    }

    /**
     * Set Element Helper
     *
     * @param  \Zend\Form\View\Helper\FormElement $elementHelper
     * @return self
     */
    public function setElementHelper(ZendFormElement $elementHelper)
    {
        $elementHelper->setView($this->getView());
        $this->elementHelper = $elementHelper;

        return $this;
    }

    /**
     * Get Element Helper
     *
     * @return \Zend\Form\View\Helper\FormElement
     */
    public function getElementHelper()
    {
        if (!$this->elementHelper) {
            $this->setElementHelper($this->view->plugin('formelement'));
        }

        return $this->elementHelper;
    }

    /**
     * Set Element Error Helper
     *
     * @param  \Zend\Form\View\Helper\FormElementErrors $errorHelper
     * @return self
     */
    public function setElementErrorHelper(FormElementErrors $errorHelper)
    {
        $errorHelper->setView($this->getView());
        $this->elementErrorHelper = $errorHelper;
        return $this;
    }

    /**
     * Get Element Error Helper
     *
     * @return \Zend\Form\View\Helper\FormElementErrors
     */
    public function getElementErrorHelper()
    {
        if (!$this->elementErrorHelper) {
            $this->setElementErrorHelper($this->view->plugin('formelementerrors'));
        }

        return $this->elementErrorHelper;
    }

    /**
     * Set Description Helper
     *
     * @param FormDescription
     * @return self
     */
    public function setDescriptionHelper(FormDescription $descriptionHelper)
    {
        $descriptionHelper->setView($this->getView());
        $this->descriptionHelper = $descriptionHelper;

        return $this;
    }

    /**
     * Get Description Helper
     *
     * @return FormDescription
     */
    public function getDescriptionHelper()
    {
        if (!$this->descriptionHelper) {
            $this->setDescriptionHelper($this->view->plugin('ztbformdescription'));
        }

        return $this->descriptionHelper;
    }

    /**
     * Set Group Wrapper
     *
     * @param  string $groupWrapper
     * @return self
     */
    public function setGroupWrapper($groupWrapper)
    {
        $this->groupWrapper = (string) $groupWrapper;

        return $this;
    }

    /**
     * Get Group Wrapper
     *
     * @return string
     */
    public function getGroupWrapper()
    {
        return $this->groupWrapper;
    }

    /**
     * Set Control Wrapper
     *
     * @param  string $controlWrapper;
     * @return self
     */
    public function setControlWrapper($controlWrapper)
    {
        $this->controlWrapper = (string) $controlWrapper;

        return $this;
    }

    /**
     * Get Control Wrapper
     *
     * @return string
     */
    public function getControlWrapper()
    {
        return $this->controlWrapper;
    }

    /**
     * Render
     *
     * @param  \Zend\Form\ElementInterface $element
     * @param  string                      $groupWrapper
     * @param  string                      $controlWrapper
     * @return string
     */
    public function render(ElementInterface $element, $groupWrapper = null, $controlWrapper = null)
    {
        $labelHelper = $this->getLabelHelper();
        $escapeHelper = $this->getEscapeHtmlHelper();
        $elementHelper = $this->getElementHelper();
        $elementErrorHelper = $this->getElementErrorHelper();
        $descriptionHelper = $this->getDescriptionHelper();
        $groupWrapper = $groupWrapper ?: $this->groupWrapper;
        $controlWrapper = $controlWrapper ?: $this->controlWrapper;
        $renderer = $elementHelper->getView();

        $hiddenElementForCheckbox = '';
        if (method_exists($element, 'useHiddenElement') && $element->useHiddenElement()) {
            // If we have hidden input with checkbox's unchecked value, render that separately so it can be prepended later, and unset it in the element
            $withHidden = $elementHelper->render($element);
            $withoutHidden = $elementHelper->render($element->setUseHiddenElement(false));
            $hiddenElementForCheckbox = str_ireplace($withoutHidden, '', $withHidden);
        }

        $id = $element->getAttribute('id') ?: $element->getAttribute('name');

        if (method_exists($renderer, 'plugin')) {
            if ($element instanceof \Zend\Form\Element\Radio) {
                $renderer->plugin('form_radio')->setLabelAttributes(array(
                    'class' => 'radio',
                ));
            } elseif ($element instanceof \Zend\Form\Element\MultiCheckbox) {
                $renderer->plugin('form_multi_checkbox')->setLabelAttributes(array(
                    'class' => 'checkbox',
                ));
            }
        }

        $controlLabel = '';
        $label = $element->getLabel();
        if (strlen($label) === 0) {
            $label = $element->getOption('label') ?: $element->getAttribute('label');
        }

        if ($label && !$element->getOption('skipLabel')) {
            $controlLabel .= $labelHelper->openTag(array(
                'class' => ($element->getOption('wrapCheckboxInLabel') ? 'checkbox' : 'control-label'),
            ) + ($element->hasAttribute('id') ? array('for' => $id) : array()));

            if (null !== ($translator = $labelHelper->getTranslator())) {
                $label = $translator->translate(
                    $label, $labelHelper->getTranslatorTextDomain()
                );
            }
            if ($element->getOption('wrapCheckboxInLabel')) {
                $controlLabel .= $elementHelper->render($element) . ' ';
            }
            if ($element->getOption('skipLabelEscape')) {
                $controlLabel .= $label;
            } else {
                $controlLabel .= $escapeHelper($label);
            }
            $controlLabel .= $labelHelper->closeTag();
            if ($element instanceof \Zend\Form\Element\Radio
                || $element instanceof \Zend\Form\Element\MultiCheckbox) {
                $controlLabel = str_replace(array('<label', '</label>'), array('<div', '</div>'), $controlLabel);
            }
        }

        $controls = '';

        if ($element->getOption('wrapCheckboxInLabel')) {
            $controls = $controlLabel;
            $controlLabel = '';
        } else {
            $controls = $elementHelper->render($element);
        }

        $html = $hiddenElementForCheckbox . $controlLabel . sprintf($controlWrapper,
            $id,
            $controls,
            $descriptionHelper->render($element),
            $elementErrorHelper->render($element)
        );

        $addtClass = ($element->getMessages()) ? ' error' : '';

        return sprintf($groupWrapper, $addtClass, $id, $html);
    }

    /**
     * Render
     *
     * @param  \Zend\Form\ElementInterface $element
     * @param  string                      $groupWrapper
     * @param  string                      $controlWrapper
     * @return string
     */
    public function renderNew(ElementInterface $element, $groupWrapper = null, $controlWrapper = null)
    {

        $wrapInLabel = ($element->getOption('wrapCheckboxInLabel') || $element->getOption('wrapInLabel'));

        $controlPart = $this->renderControlPart($element, $controlWrapper);

        if ($wrapInLabel) {

            $labelPart = $this->renderLabelPart($element, $attributes = array(), $controlPart);
            $controlPart = '';
        } else {
            $labelPart = $this->renderLabelPart($element, $attributes = array('class' => 'control-label'));
        }

        $descriptionPart = $this->renderDescriptionPart($element);

        $errorsPart = $this->renderErrorsPart($element);

// render before stuff (hidden inputs for checkboxes)
// render label / controls -- changes based on whether label wraps element or not
// render stuff after (help text, errors)
// wrap everything in group wrapper

        // Utility hidden input to normalize checkbox values
        $hiddenPart = $this->renderHiddenPart($element);

        // Assemble parts


        if ($element instanceof \Zend\Form\Element\Button) {
            $contents = $controlPart . $descriptionPart . $errorsPart;
        } else {
            $contents = $hiddenPart . $labelPart . $controlPart . $descriptionPart . $errorsPart;
        }

        // @todo add in controlWrapper error / success class
//        if ($controlWrapper) {
//            $html = sprintf($controlWrapper, $element->getAttribute('id') ?: $element->getAttribute('name'));
//        }

        // .form-group attributes
        $attributes = array();

        $attributes['class'] = '';

        // note: this id will get prepended by the group wrapper
        $attributes['id'] = $element->getAttribute('id') ?: $element->getAttribute('name');

        if ($element->getMessages()) {
            $attributes['class'] = trim($attributes['class'] . ' error');
        }
        if ($element instanceof \Zend\Form\Element\Button) {
            $groupWrapper = '';
        }

        return $this->renderGroup($element, $groupWrapper, $attributes, $contents);

    }


    /**
     * Render container group around label and element(s)
     *
     * @param  \Zend\Form\ElementInterface $element
     * @param  string                      $groupWrapper
     * @param  array                       $attributes
     * @param  string                      $contents
     * @return string
     */
    public function renderGroup(ElementInterface $element, $groupWrapper = null, $attributes = array(), $contents = '')
    {
        // Default to empty strings
        $attributes = $attributes + array(
            'class' => '',
            'id' => ''
        );

        if ($groupWrapper === null) {
            $groupWrapper = $this->groupWrapper;
        }


        if ($element instanceof \Zend\Form\Element\Checkbox
            || $element instanceof \Zend\Form\Element\MultiCheckbox) {
            $attributes['class'] = trim($attributes['class'] . ' checkbox');
        } elseif ($element instanceof \Zend\Form\Element\Radio) {
            $attributes['class'] = trim($attributes['class'] . ' radio');
        } else {
            $attributes['class'] = trim($attributes['class'] . ' form-group');
        }

        if ($groupWrapper) {
            return sprintf($groupWrapper,
                $attributes['class'],
                $attributes['id'],
                $contents
            );
        }
        return $contents;
    }

/*
  Vanilla example:
  <div class="form-group">
    <label for="exampleInputEmail1">Email address</label>
    <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Enter email">
  </div>

  Checkbox with implicit label:
  <div class="checkbox">
    <label>
      <input type="checkbox"> Check me out
    </label>
  </div>

  Sizing with column classes:
  <div class="form-group">
    <label for="inputEmail3" class="col-sm-2 control-label">Email</label>
    <div class="col-sm-10">
      <input type="email" class="form-control" id="inputEmail3" placeholder="Email">
    </div>
  </div>

  Inline implicit label:
  <label class="checkbox-inline">
    <input type="checkbox" id="inlineCheckbox1" value="option1"> 1
  </label>


*/

    /**
     * Render the label part of a bootstrap 3 form group
     *
     * Infrequently Asked Questions about form labels
     *   Q: Do I really need a `<label>`?
     *   A: Yes. Screen readers.
     *   Q: What if I don't want it to show up on the page though? It ruins my design.
     *   A: Still put it in the markup, but move it off-screen with a `.sr-only` class. DO NOT set `display: none` on it.
                You could also use the built-in 'hideLabel' FormElement option in this library.
     *   Q: Do I really need a `for` attribute in the label?
     *   A: Yes. Screen readers.
     *   Q: Can the `for` attribute match the element's `name` attribute?
     *   A: No! It has to match the element's `id` attribute
     *   Q: But I don't have an `id`!
     *   A: Add one in.
     *   Q: What if I have an implicit label that wraps my element? Surely I don't need a `for` attribute then!
     *   A: Implicit labels that wrap elements might be valid HTML5, but screen readers can still choke if there's no `for` attribute.
     *          Just add one in. http://www.w3.org/TR/WCAG20-TECHS/H44.html#ua2.18.1
     *   Q: What if it's a label for plain text?
     *   A: Arguably that's semanitically wrong... but it *is* in the Bootstrap doco... Fine, I'll give you this one. No `for` attribute if it's plain text.
     *
     * @param   \Zend\Form\ElementInterface  $element
     * @param   string                       $appendedContent  (if the label wraps the form element, pass that in here and render it inside the label)
     * @return  string
     */
    public function renderLabelPart(ElementInterface $element, $attributes = array(), $wrappedContent = '')
    {

        $helper       = $this->getLabelHelper();
        $escapeHelper = $this->getEscapeHtmlHelper();

        $attributes = $element->getLabelAttributes() ?: array() + $attributes;

        if ($id = $element->getAttribute('id')) {
            $attributes['for'] = $id;
        }

        if ($element->getOption('skipLabel')) {
            exit('skipLabel is deprecated!'); // @todo fix this... maybe assume this means hideLabel and throw a notice?
        }
        if ($element->getOption('hideLabel')) {
            if (!isset($attributes)) {
                $attributes['class'] = '';
            }
            $attributes['class'] = trim($attributes['class'] . ' sr-only');
        }

//             'class' => ($element->getOption('wrapCheckboxInLabel') ? 'checkbox' : 'control-label'),
//            ) + ($element->hasAttribute('id') ? array('for' => $id) : array()));

        $labelOpenTag = $helper->openTag($attributes ?: array());

        $labelText = $element->getLabel();

        if (strlen($labelText) === 0) {
            $labelText = $element->getOption('label') ?: $element->getAttribute('label');
        }

        if (null !== ($translator = $helper->getTranslator())) {
            $labelText = $translator->translate(
                $labelText, $helper->getTranslatorTextDomain()
            );
        }

        if (!$element->getOption('skipLabelEscape')) {
            $labelText = $escapeHelper($labelText);
        }

        if ($wrappedContent) {
            $labelText .= ' ' . $wrappedContent;
        }

        // @todo figure out a better way to do this
//        if ($element->getOption('wrapCheckboxInLabel')) {
//            $labelText = $elementHelper->render($element) . ' ' . $labelText;
//        }

        $labelCloseTag = $helper->closeTag();



        return $labelOpenTag . $labelText . $labelCloseTag;




        // @todo figure out a better way to do this
/*
        if ($element instanceof \Zend\Form\Element\Radio
            || $element instanceof \Zend\Form\Element\MultiCheckbox) {
                $controlLabel = str_replace(array('<label', '</label>'), array('<div', '</div>'), $controlLabel);
        }

        $controls = '';

        if ($element->getOption('wrapCheckboxInLabel')) {
            $controls = $controlLabel;
            $controlLabel = '';
        } else {
            $controls = $elementHelper->render($element);
        }

        // i.e. '<div class="col-sm-10">%s</div>'
        if ($controlWrapper) {
            $html = $hiddenElementForCheckbox . $controlLabel . sprintf($controlWrapper,
                $id,
                $controls,
                $descriptionHelper->render($element),
                $elementErrorHelper->render($element)
            );
        }
        $html = $hiddenElementForCheckbox . $controlLabel . sprintf($controlWrapper,
            $id,
            $controls,
            $descriptionHelper->render($element),
            $elementErrorHelper->render($element)
        );


        $addtClass = ($element->getMessages()) ? ' error' : '';

        return sprintf($groupWrapper, $addtClass, $id, $html);



*/

    }

    public function renderControlPart(ElementInterface $element, $controlWrapper)
    {
        // Wrong way to do to this... @todo make subclasses of the form view helpers
        if (!($element instanceof \Zend\Form\Element\Radio)
            && !($element instanceof \Zend\Form\Element\Checkbox)
            && !($element instanceof \Zend\Form\Element\MultiCheckbox)
            && !($element instanceof \Zend\Form\Element\File)
            && !($element instanceof \Zend\Form\Element\Button)) {
            $class = $element->getAttribute('class') ?: '';
            $element->setAttribute('class', trim($class . ' form-control'));
        }

        $controlContent = $this->getElementHelper()->render($element);

        $controlWrapper = $controlWrapper ?: $this->controlWrapper;

        if ($controlWrapper) {
            return sprintf($controlWrapper, $controlContent);
        }

        return $controlContent;
    }

    public function renderDescriptionPart(ElementInterface $element)
    {
        return $this->getDescriptionHelper()->render($element);
    }

    public function renderErrorsPart(ElementInterface $element)
    {
        return $this->getElementErrorHelper()->render($element);
    }

    public function renderHiddenPart(ElementInterface $element)
    {
        $elementHelper = $this->getElementHelper();

        $hiddenPart = '';
        if (method_exists($element, 'useHiddenElement') && $element->useHiddenElement()) {
            // If we have hidden input with checkbox's unchecked value, render that separately so it can be prepended later, and unset it in the element
            $withHidden = $elementHelper->render($element);
            $withoutHidden = $elementHelper->render($element->setUseHiddenElement(false));
            $hiddenPart = str_ireplace($withoutHidden, '', $withHidden);
        }
        return $hiddenPart;

    }


    /**
     * Magical Invoke
     *
     * @param  \Zend\Form\ElementInterface $element
     * @param  string                      $groupWrapper
     * @param  string                      $controlWrapper
     * @return string|self
     */
    public function __invoke(ElementInterface $element = null, $groupWrapper = null, $controlWrapper = null)
    {
        if ($element) {
//            return $this->render($element, $groupWrapper, $controlWrapper);
            return $this->renderNew($element, $groupWrapper, $controlWrapper);
        }

        return $this;
    }
}
