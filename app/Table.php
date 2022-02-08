<?php
// Lemmod - Couldn't be bothered to make my own class as this works as well

/**
 * STable - Generate HTML Tables
 *  
 * @package STable
 * @category STable
 * @name STable
 * @version 1.0
 * @author Shay Anderson 03.11
 */
final class STable {
      /**
       * End of line string (default \n)
       */
      const EOF_LINE = "\n";

      /**
       * Current node ID
       *  
       * @var int $_node_id
       */
      private $_node_id = 0;

      /**
       * HTML table
       *  
       * @var string $_table
       */
      private $_table;

      /**
       * thead parts
       *
       * @var array $_thead
       */
      private $_thead = array();

      /**
       * tr parts
       *
       * @var array $_tr
       */
      private $_tr = array();

      /**
       * Table attributes
       *
       * @var string $attributes
       */
      public $attributes;

      /**
       * Table border width
       *
       * @var int $border
       */
      public $border = 0;

      /**
       * Table cell padding width
       *
       * @var int $cellpadding
       */
      public $cellpadding = 0;

      /**
       * Table cell spacing width
       *
       * @var int $cellspacing
       */
      public $cellspacing = 0;

      /**
       * Table class
       *
       * @var string $class
       */
      public $class;

      /**
       * Table ID
       *
       * @var string $id
       */
      public $id;

      /**
       * Table width
       *
       * @var mixed width
       */
      public $width;

      /**
       * Set params
       *
       * @param string $id
       */
      public function  __construct($id = null) {
            // set table ID
            $this->id = $id;
      }

      /**
       * Format table class attribute
       *
       * @param string $class
       */
      private function _formatAttributeClass($class = null) {
            return $class ? " class=\"{$class}\"" : null;
      }

      /**
       * Format table attributes
       *
       * @param string $attributes
       */
      private function _formatAttributes($attributes = null) {
            return $attributes ? " {$attributes}" : null;
      }

      /**
       * Current node ID getter
       *
       * @return int
       */
      private function _getNodeId() {
            // return node ID
            return $this->_node_id;
      }

      /**
       * Current node ID setter
       */
      private function _setNodeId() {
            // increment new node ID
            $this->_node_id++;
      }

      /**
       * tbody getter
       *
       * @return string
       */
      private function _getTbody() {
            $html = null;

            // add tr(s)
            foreach($this->_tr as $tr) {
                  // add tr and close tr
                  $html .= "{$tr}</tr>" . self::EOF_LINE;
            }

            return $html;
      }

      /**
       * thead getter
       *
       * @return string
       */
      private function _getThead() {
            $html = null;

            // add thead(s)
            foreach($this->_thead as $thead) {
                  // add thead and close thead
                  $html .= "{$thead}</thead>" . self::EOF_LINE;
            }

            return $html;
      }

      /**
       * Table td setter
       *
       * @param mixed $text
       * @param string $class
       * @param string $attributes
       * @return STable
       */
      public function td($text = null, $class = null, $attributes = null) {
            // add td to current tr
            $this->_tr[$this->_getNodeId()] .= "<td{$this->_formatAttributeClass($class)}{$this->_formatAttributes($attributes)}>"
                  . "{$text}</td>" . self::EOF_LINE;

            return $this;
      }

      /**
       * Table th setter
       *
       * @param mixed $text
       * @param string $class
       * @param string $attibutes
       * @return STable
       */
      public function th($text = null, $class = null, $attributes = null) {
            // add th to current thead
            $this->_thead[$this->_getNodeId()] .= "<th{$this->_formatAttributeClass($class)}{$this->_formatAttributes($attributes)}>"
                  . "{$text}</th>" . self::EOF_LINE;

            return $this;
      }

      /**
       * Table thead setter
       *
       * @param string $class
       * @param string $attibutes
       * @return STable
       */
      public function thead($class = null, $attributes = null) {
            // set new node ID
            $this->_setNodeId();

            // add thead
            $this->_thead[$this->_getNodeId()] = "<thead{$this->_formatAttributeClass($class)}{$this->_formatAttributes($attributes)}>"
                  . self::EOF_LINE;

            return $this;
      }

      /**
       * Table tr setter
       *
       * @param string $class
       * @param string $attributes
       * @return STable
       */
      public function tr($class = null, $attributes = null) {
            // set new node ID
            $this->_setNodeId();

            // add tr
            $this->_tr[$this->_getNodeId()] = "<tr{$this->_formatAttributeClass($class)}{$this->_formatAttributes($attributes)}>"
                  . self::EOF_LINE;

            return $this;
      }

      /**
       * Table HTML getter
       *
       * @return string
       */
      public function getTable() {
            // return table HTML
            return "<table border=\"{$this->border}\""
                  // set ID if set, set class and attributes
                  . ( $this->id ? " id=\"{$this->id}\"" : null ) . $this->_formatAttributeClass($this->class)
                  . $this->_formatAttributes($this->attributes)

                  // set width if set
                  . ( $this->width ? " width=\"{$this->width}\"" : null )

                  // set table params
                  . " cellpadding=\"{$this->cellpadding}\" cellspacing=\"{$this->cellspacing}\">" . self::EOF_LINE

                  // add table thead and tbody
                  . $this->_getThead() . $this->_getTbody()

                  // add table HTML
                  . $this->_table

                  // close table
                  . "</table>" . self::EOF_LINE;
      }
}