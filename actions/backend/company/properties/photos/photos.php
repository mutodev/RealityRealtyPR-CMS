<?php

try {

    $debug = true;
    ini_set('memory_limit', -1);

    //Get Helpers
    $Flash = helper('Flash');
    $Form = helper('Form');

    $id = get('id');
    $edit = !empty($id);

    //Define constants
    $MODEL_CLASS = "PropertyPhoto";
    $MODEL_NAME = t("Photos");
    $MODEL_TEMPLATE = "%{id}";
    $REDIRECT_URL = url("..view?id=" . $id);

    $q = new Doctrine_Query();
    $q->from('Property n');
    $q->leftJoin('n.Photos p');
    $q->andWhere('id = ?', $id);
    $q->andWhere('company_id = ?', Auth::get()->getActiveCompany()->id);
    $Model = $q->fetchOne();

    //Validate that the model exists
    if (empty($Model)) {
        $Flash->error(t('Could not find %0 for editing', $MODEL_NAME), $REDIRECT_URL);
    }

    if (!$Form->isSubmitted()) {
        $photos = array();
        if (isset($Model->Photos)) {
            foreach ($Model->Photos as $k => $Photo) {
                $photos[$k]['file'] = $Photo->original;
                $photos[$k]['description'] = $Photo->description;
                $photos[$k]['is_approved'] = $Photo->is_approved;
            }
        }

        $Form->setValues(array('photos' => $photos));
    }

    if ($Form->isValid()) {

        $values = $Form->getValues();
        $photos = (array)$values['photos'];

        foreach ($photos as $k => $photo) {
            $photos[$k]['url'] = $photo['file'];
        }

        Doctrine_Query::create()->delete('PropertyPhoto p')->where('p.property_id = ?', $Model->id)->execute();

        //Format photos
        foreach ($photos as $k => $photo) {
            $values = uploadPhotoToS3($photo['file'], $id, ($k + 1));
            $values['description'] = $photo['description'];
            $values['is_approved'] = Auth::hasRole('company.manager');

            if (Auth::hasRole('company.manager')) {
                $values['is_approved'] = $photo['is_approved'];
            }
            $values['property_id'] = $id;

            $PropertyPhoto = new PropertyPhoto();
            $PropertyPhoto->original = $values['original'];
            $PropertyPhoto->large = $values['large'];
            $PropertyPhoto->medium = $values['medium'];
            $PropertyPhoto->small  = $values['small'];
            $PropertyPhoto->description = $values['description'];
            $PropertyPhoto->is_approved = $values['is_approved'];
            $PropertyPhoto->property_id = $values['property_id'];
            $PropertyPhoto->save();
        }

        //Redirect
        $flash_msg = t("Sucesfully %{action} %{model} for &quot;<i><a href='%{url}'>#%{name}</a></i>&quot;", array(
            "action" => $edit ? t("updated") : t("created"),
            "model" => $MODEL_NAME,
            "url" => url("..view?id=" . $Model->id),
            "name" => Template::create($MODEL_TEMPLATE)->apply($Model),
        ));

        $Flash->success($flash_msg, $REDIRECT_URL);
    }

} catch (Exception $e) {
    prd($e->getMessage());

}

    Action::set(compact('breadcrumb'));
