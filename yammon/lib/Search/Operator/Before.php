<?php

    class Search_Operator_Before extends Search_Operator_LessThanOrEqual{

        /* Returns a caption for the operator */
        function description( ){
            return t("Is Before");
        }

    }
