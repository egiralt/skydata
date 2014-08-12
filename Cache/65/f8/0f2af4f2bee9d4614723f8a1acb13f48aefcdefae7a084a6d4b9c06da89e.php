<?php

/* angular_js.twig */
class __TwigTemplate_65f80f2af4f2bee9d4614723f8a1acb13f48aefcdefae7a084a6d4b9c06da89e extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "angular.module('SkyDataApp')
\t.controller ('";
        // line 2
        echo twig_escape_filter($this->env, (isset($context["serviceName"]) ? $context["serviceName"] : null), "html", null, true);
        echo "SvcCtrl', ['\$scope', '\$http',
\t\tfunction (\$scope, \$http) {
\t\t\t";
        // line 4
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["tables"]) ? $context["tables"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["table"]) {
            // line 5
            echo "\t\t\t
\t\t\t";
            // line 7
            echo "\t\t\t";
            if ((twig_length_filter($this->env, $this->getAttribute((isset($context["table"]) ? $context["table"] : null), "fields")) > 1)) {
                echo " 
\t\t\t\$scope.";
                // line 8
                echo twig_escape_filter($this->env, $this->getAttribute((isset($context["table"]) ? $context["table"] : null), "name"), "html", null, true);
                echo " =";
                if ($this->getAttribute((isset($context["table"]) ? $context["table"] : null), "is_enumerable")) {
                    echo " []";
                } else {
                    echo " {}";
                }
                echo ";
\t\t\t";
            } else {
                // line 9
                echo " 
\t\t\t\$scope.";
                // line 10
                echo twig_escape_filter($this->env, $this->getAttribute((isset($context["table"]) ? $context["table"] : null), "name"), "html", null, true);
                echo " =";
                if ($this->getAttribute((isset($context["table"]) ? $context["table"] : null), "is_enumerable")) {
                    echo " []";
                } else {
                    echo " null";
                }
                echo ";
\t\t\t";
            }
            // line 12
            echo "\t\t\t
\t\t\t";
            // line 13
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute((isset($context["table"]) ? $context["table"] : null), "methods"));
            foreach ($context['_seq'] as $context["_key"] => $context["method"]) {
                // line 14
                echo "\t\t\t\$scope.";
                echo twig_escape_filter($this->env, $this->getAttribute((isset($context["method"]) ? $context["method"] : null), "name"), "html", null, true);
                echo " = function (";
                if ((twig_length_filter($this->env, $this->getAttribute((isset($context["method"]) ? $context["method"] : null), "parameters")) > 0)) {
                    echo "_";
                    echo twig_escape_filter($this->env, twig_join_filter($this->getAttribute((isset($context["method"]) ? $context["method"] : null), "parameters"), ", _"), "html", null, true);
                }
                echo ") {
\t\t\t\t\$http.get ('service/";
                // line 15
                echo twig_escape_filter($this->env, (isset($context["serviceName"]) ? $context["serviceName"] : null), "html", null, true);
                echo "/";
                echo twig_escape_filter($this->env, $this->getAttribute((isset($context["method"]) ? $context["method"] : null), "name"), "html", null, true);
                echo "'";
                if ((twig_length_filter($this->env, $this->getAttribute((isset($context["method"]) ? $context["method"] : null), "parameters")) > 0)) {
                    echo ",
\t\t\t\t {
\t\t\t\t\tparams: {
\t\t\t\t\t";
                    // line 18
                    $context['_parent'] = (array) $context;
                    $context['_seq'] = twig_ensure_traversable($this->getAttribute((isset($context["method"]) ? $context["method"] : null), "parameters"));
                    foreach ($context['_seq'] as $context["_key"] => $context["parameter"]) {
                        // line 19
                        echo "\t\t\t\t\t";
                        echo twig_escape_filter($this->env, (isset($context["parameter"]) ? $context["parameter"] : null), "html", null, true);
                        echo " : _";
                        echo twig_escape_filter($this->env, (isset($context["parameter"]) ? $context["parameter"] : null), "html", null, true);
                        echo " ";
                        if (((isset($context["parameter"]) ? $context["parameter"] : null) != twig_last($this->env, $this->getAttribute((isset($context["method"]) ? $context["method"] : null), "parameters")))) {
                            echo ", ";
                        }
                        echo " 
\t\t\t\t\t";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['parameter'], $context['_parent'], $context['loop']);
                    $context = array_intersect_key($context, $_parent) + $_parent;
                    // line 21
                    echo "\t\t\t\t}
\t\t\t\t";
                }
                // line 22
                echo " ";
                // line 23
                echo "\t\t\t\t). success ( function (data) {
\t\t\t\t\t";
                // line 24
                if ($this->getAttribute((isset($context["table"]) ? $context["table"] : null), "is_enumerable")) {
                    echo " \t";
                    // line 25
                    echo "\t\t\t\t\tfor (idx in data) {
\t\t\t\t\t\t";
                    // line 26
                    if ((twig_length_filter($this->env, $this->getAttribute((isset($context["table"]) ? $context["table"] : null), "fields")) > 1)) {
                        echo "  \t\t";
                        // line 27
                        echo "\t\t\t\t\t\t\$scope.";
                        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["table"]) ? $context["table"] : null), "name"), "html", null, true);
                        echo ".push({
\t\t\t\t\t\t";
                        // line 28
                        $context['_parent'] = (array) $context;
                        $context['_seq'] = twig_ensure_traversable($this->getAttribute((isset($context["table"]) ? $context["table"] : null), "fields"));
                        foreach ($context['_seq'] as $context["_key"] => $context["field"]) {
                            // line 29
                            echo "\t\t\t\t\t\t\t";
                            echo twig_escape_filter($this->env, (isset($context["field"]) ? $context["field"] : null), "html", null, true);
                            echo ": data[idx].";
                            echo twig_escape_filter($this->env, (isset($context["field"]) ? $context["field"] : null), "html", null, true);
                            if (((isset($context["field"]) ? $context["field"] : null) != twig_last($this->env, $this->getAttribute((isset($context["table"]) ? $context["table"] : null), "fields")))) {
                                echo ",";
                            }
                            // line 30
                            echo "\t\t\t\t\t\t";
                        }
                        $_parent = $context['_parent'];
                        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['field'], $context['_parent'], $context['loop']);
                        $context = array_intersect_key($context, $_parent) + $_parent;
                        echo " \t";
                        // line 31
                        echo "\t\t\t\t\t\t});
\t\t\t\t\t\t";
                    } else {
                        // line 32
                        echo "\t";
                        // line 33
                        echo "\t\t\t\t\t\t\$scope.";
                        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["table"]) ? $context["table"] : null), "name"), "html", null, true);
                        echo ".push(data.";
                        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["table"]) ? $context["table"] : null), "fields"), 0, array(), "array"), "html", null, true);
                        echo ");
\t\t\t\t\t\t";
                    }
                    // line 35
                    echo "\t\t\t\t\t}
\t\t\t\t\t";
                } else {
                    // line 36
                    echo " ";
                    // line 37
                    echo "\t\t\t\t\t\t";
                    if ((twig_length_filter($this->env, $this->getAttribute((isset($context["table"]) ? $context["table"] : null), "fields")) > 1)) {
                        echo " \t\t";
                        // line 38
                        echo "\t\t\t\t\t\t\$scope.";
                        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["table"]) ? $context["table"] : null), "name"), "html", null, true);
                        echo " = {
\t\t\t\t\t\t";
                        // line 39
                        $context['_parent'] = (array) $context;
                        $context['_seq'] = twig_ensure_traversable($this->getAttribute((isset($context["table"]) ? $context["table"] : null), "fields"));
                        foreach ($context['_seq'] as $context["_key"] => $context["field"]) {
                            // line 40
                            echo "\t\t\t\t\t\t\t";
                            echo twig_escape_filter($this->env, (isset($context["field"]) ? $context["field"] : null), "html", null, true);
                            echo ": data.";
                            echo twig_escape_filter($this->env, (isset($context["field"]) ? $context["field"] : null), "html", null, true);
                            if (((isset($context["field"]) ? $context["field"] : null) != twig_last($this->env, $this->getAttribute((isset($context["table"]) ? $context["table"] : null), "fields")))) {
                                echo ",";
                            }
                            // line 41
                            echo "\t\t\t\t\t\t";
                        }
                        $_parent = $context['_parent'];
                        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['field'], $context['_parent'], $context['loop']);
                        $context = array_intersect_key($context, $_parent) + $_parent;
                        echo " ";
                        // line 42
                        echo "\t\t\t\t\t\t};
\t\t\t\t\t\t";
                    } else {
                        // line 43
                        echo " \t\t";
                        // line 44
                        echo "\t\t\t\t\t\t\$scope.";
                        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["table"]) ? $context["table"] : null), "name"), "html", null, true);
                        echo ".";
                        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["table"]) ? $context["table"] : null), "fields"), 0, array(), "array"), "html", null, true);
                        echo " = data.";
                        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["table"]) ? $context["table"] : null), "fields"), 0, array(), "array"), "html", null, true);
                        echo ";
\t\t\t\t\t\t";
                    }
                    // line 46
                    echo "\t\t\t\t\t";
                }
                echo " ";
                // line 47
                echo "\t\t\t\t});
\t\t\t};\t\t\t
\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['method'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 49
            echo " ";
            // line 50
            echo "\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['table'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        echo " ";
        // line 51
        echo "\t\t\t
\t\t";
        // line 53
        echo "\t\t";
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["methods"]) ? $context["methods"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["method"]) {
            // line 54
            echo "\t\t\t";
            if ((twig_length_filter($this->env, $this->getAttribute((isset($context["method"]) ? $context["method"] : null), "bind_variable")) > 0)) {
                // line 55
                echo "\t\t\t\$scope.";
                echo twig_escape_filter($this->env, $this->getAttribute((isset($context["method"]) ? $context["method"] : null), "bind_variable"), "html", null, true);
                echo " = null; ";
                // line 56
                echo "\t\t\t";
            }
            // line 57
            echo "\t\t\t\$scope.";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["method"]) ? $context["method"] : null), "name"), "html", null, true);
            echo " = function (";
            if ((twig_length_filter($this->env, $this->getAttribute((isset($context["method"]) ? $context["method"] : null), "parameters")) > 0)) {
                echo "_";
                echo twig_escape_filter($this->env, twig_join_filter($this->getAttribute((isset($context["method"]) ? $context["method"] : null), "parameters"), ", _"), "html", null, true);
            }
            echo ") {
\t\t\t\t\$http.get ('service/";
            // line 58
            echo twig_escape_filter($this->env, (isset($context["serviceName"]) ? $context["serviceName"] : null), "html", null, true);
            echo "/";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["method"]) ? $context["method"] : null), "name"), "html", null, true);
            echo "'";
            if ((twig_length_filter($this->env, $this->getAttribute((isset($context["method"]) ? $context["method"] : null), "parameters")) > 0)) {
                echo ",
\t\t\t\t {
\t\t\t\t\tparams: {
\t\t\t\t\t";
                // line 61
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute((isset($context["method"]) ? $context["method"] : null), "parameters"));
                foreach ($context['_seq'] as $context["_key"] => $context["parameter"]) {
                    // line 62
                    echo "\t\t\t\t\t";
                    echo twig_escape_filter($this->env, (isset($context["parameter"]) ? $context["parameter"] : null), "html", null, true);
                    echo " : _";
                    echo twig_escape_filter($this->env, (isset($context["parameter"]) ? $context["parameter"] : null), "html", null, true);
                    echo " ";
                    if (((isset($context["parameter"]) ? $context["parameter"] : null) != twig_last($this->env, $this->getAttribute((isset($context["method"]) ? $context["method"] : null), "parameters")))) {
                        echo ", ";
                    }
                    echo " 
\t\t\t\t\t";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['parameter'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 64
                echo "\t\t\t\t\t}
\t\t\t\t}
\t\t\t\t";
            }
            // line 66
            echo ")";
            if ((twig_length_filter($this->env, $this->getAttribute((isset($context["method"]) ? $context["method"] : null), "bind_variable")) > 0)) {
                echo ".success(function (data) {
\t\t\t\t\$scope.";
                // line 67
                echo twig_escape_filter($this->env, $this->getAttribute((isset($context["method"]) ? $context["method"] : null), "bind_variable"), "html", null, true);
                echo " = data;
\t\t\t})";
            }
            // line 68
            echo ";
\t\t\t};
\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['method'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 70
        echo "\t\t\t
\t\t
\t\t";
        // line 73
        echo "\t\t";
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["tables"]) ? $context["tables"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["table"]) {
            // line 74
            echo "\t\t\t";
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute((isset($context["table"]) ? $context["table"] : null), "methods"));
            foreach ($context['_seq'] as $context["_key"] => $context["method"]) {
                // line 75
                echo "\t\t\t\t";
                if ($this->getAttribute((isset($context["method"]) ? $context["method"] : null), "run_on_load")) {
                    // line 76
                    echo "\t\t\t\t\$scope.";
                    echo twig_escape_filter($this->env, $this->getAttribute((isset($context["method"]) ? $context["method"] : null), "name"), "html", null, true);
                    echo "();
\t\t\t\t";
                }
                // line 78
                echo "\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['method'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 79
            echo "\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['table'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 80
        echo "\t\t";
        // line 81
        echo "\t\t";
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["methods"]) ? $context["methods"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["method"]) {
            // line 82
            echo "\t\t\t";
            if ($this->getAttribute((isset($context["method"]) ? $context["method"] : null), "run_on_load")) {
                // line 83
                echo "\t\t\t\$scope.";
                echo twig_escape_filter($this->env, $this->getAttribute((isset($context["method"]) ? $context["method"] : null), "name"), "html", null, true);
                echo "();
\t\t\t";
            }
            // line 85
            echo "\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['method'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 86
        echo "\t\t
\t\t}
\t]);

";
    }

    public function getTemplateName()
    {
        return "angular_js.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  374 => 86,  368 => 85,  362 => 83,  359 => 82,  354 => 81,  352 => 80,  346 => 79,  340 => 78,  334 => 76,  331 => 75,  326 => 74,  321 => 73,  317 => 70,  309 => 68,  304 => 67,  299 => 66,  294 => 64,  279 => 62,  275 => 61,  265 => 58,  255 => 57,  252 => 56,  248 => 55,  245 => 54,  240 => 53,  237 => 51,  230 => 50,  228 => 49,  220 => 47,  216 => 46,  206 => 44,  204 => 43,  200 => 42,  193 => 41,  185 => 40,  181 => 39,  176 => 38,  172 => 37,  170 => 36,  166 => 35,  158 => 33,  156 => 32,  152 => 31,  145 => 30,  137 => 29,  133 => 28,  128 => 27,  125 => 26,  122 => 25,  119 => 24,  116 => 23,  114 => 22,  110 => 21,  95 => 19,  91 => 18,  81 => 15,  71 => 14,  67 => 13,  64 => 12,  53 => 10,  50 => 9,  39 => 8,  34 => 7,  31 => 5,  27 => 4,  22 => 2,  19 => 1,);
    }
}
