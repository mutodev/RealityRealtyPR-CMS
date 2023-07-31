<?php

    class Search_Operator_After extends Search_Operator_GreaterThanOrEqual {

        /* Returns a caption for the operator */
        function description( ){
            return t("Is After");
        }

    }
