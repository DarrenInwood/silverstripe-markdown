<?php

define('MARKDOWN_MODULE_BASE', basename(dirname(__FILE__)));


MarkdownExtension::ReplaceHTMLFields();

MarkdownEditorField::include_default_js();