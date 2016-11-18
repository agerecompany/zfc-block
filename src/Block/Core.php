<?php
/**
 * Core block
 *
 * @category Agere
 * @package Agere_View
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 13.04.15 16:09
 */
namespace Agere\Block\Block;

use Zend\View\Helper\Url;
use Zend\Stdlib\Exception\LogicException;
use Agere\Block\Service\Plugin\BlockPluginInterface;

class Core implements BlockPluginInterface
{
    protected $renderer;

    protected $header = '';

    protected $template = '';

    protected $translatorTextDomain = null;

    protected $accessor;

    protected $data = [];

    protected $variablesKey = 'variables';

    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function setTranslatorTextDomain($translator)
    {
        $this->translatorTextDomain = $translator;

        return $this;
    }

    public function getTranslatorTextDomain()
    {
        return $this->translatorTextDomain;
    }

    /**
     * Set object which check access to resource
     *
     * @param $accessor Object must implement only one method "hasAccess($resource)"
     * @return $this
     */
    public function setAccessor($accessor)
    {
        $this->accessor = $accessor;

        return $this;
    }

    public function getAccessor()
    {
        return $this->accessor;
    }

    public function hasAccess($params)
    {
        if (($accessor = $this->getAccessor())) {
            /** @var Url $urlPlugin */
            $urlPlugin = $this->getRenderer()->plugin('url');
            $route = key($params);
            $params = current($params);
            $resource = $urlPlugin($route, $params);
            $target = $params['controller'] . '/' . $params['action'];
            /** @var \Magere\Users\View\Helper\User $accessor */
            if (!$accessor->hasAccess($target) && !$accessor->hasAccess($resource)) {
                return false;
            }
        }
        return true;
    }

    public function renderAttrs($attrs)
    {
        if (!is_array($attrs)) {
            return false;
        }
        $attrsStr = '';
        //\Zend\Debug\Debug::dump($attrs);
        foreach ($attrs as $name => $value) {
            $attrsStr .= $this->renderAttr($name, $value);
        }

        return $attrsStr;
    }

    public function renderAttr($name, $value)
    {
        $attr = '';
        if ($value) {
            $attr .= sprintf('%s="%s"', $name, $value);
        } else {
            $attr .= $name . '';
        }

        return $attr;
    }

    public function setRenderer($renderer)
    {
        $this->renderer = $renderer;

        return $this;
    }

    public function getRenderer()
    {
        return $this->renderer;
    }

    public function set($name, $value)
    {
        $this->data[$name] = $value;

        return $this;
    }

    public function concat($name, $value)
    {
        $this->data[$name] .= $value;

        return $this;
    }

    public function get($name)
    {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }

    /**
     * Set template variables
     *
     * @param array $variables
     * @return $this
     */
    public function setVariables(array $variables)
    {
        $this->data[$this->variablesKey] = $variables;

        return $this;
    }

    /**
     * Get template variables
     *
     * @return array
     */
    public function getVariables()
    {
        return $this->data[$this->variablesKey];
    }

    /**
     * Set template variable
     *
     * @param $name
     * @param $value
     * @return $this
     */
    public function setVariable($name, $value)
    {
        $this->data[$this->variablesKey][$name] = $value;

        return $this;
    }

    /**
     * Get template variable
     *
     * @param $name
     * @return mixed
     */
    public function getVariable($name)
    {
        return isset($this->data[$this->variablesKey][$name]) ? $this->data[$this->variablesKey][$name] : null;
    }
}