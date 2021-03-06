<?php

class EventController extends Controller {

    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = '//layouts/column1';

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id) {
        $this->render('view', array(
            'model' => $this->loadModel($id),
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate() {
        $model = new Event;


        if (isset($_GET['calData']['start']) && $_GET['calData']['start'] != null)
            $calData['start'] = $_GET['calData']['start'];

        $calData['projectOID'] = $_GET['calData']['projectOID'];

        //// Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['Event'])) {
            $model->attributes = $_POST['Event'];
            $model->validate();
            if ($model->save()) {
                echo CJSON::encode(Generic::parseCalendarEvents($model->attributes));
                Yii::app()->end();
            } else {
                echo CJSON::encode(CHtml::errorSummary($model));
                Yii::app()->end();
            }
        }

        $this->renderPartial('create', array('model' => $model, 'calData' => $calData));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id) {
        $model = $this->loadModel($id);

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['Event'])) {
            $model->attributes = $_POST['Event'];
            if ($model->save()) {
                echo $this->renderPartial('/project/_calendar',
                        array('model' => $model->project), false, true);
                Yii::app()->end();
            } else {
                echo CJSON::encode(array('status' => 'f', 'response' => CHtml::errorSummary($model)));
                Yii::app()->end();
            }
        }

        $this->renderPartial('update', array(
            'model' => $model, false, true
        ));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id) {
        $id = $_GET['id'];

        if (Yii::app()->request->isAjaxRequest && Yii::app()->request->isPostRequest) {
            // we only allow deletion via POST request
            $model = $this->loadModel($id);
            if ($model->delete()) {
                echo $this->renderPartial('/project/_calendar',
                        array('model' => $model->project), false, true);
                Yii::app()->end();
            } else {
                echo CJSON::encode(array('status' => 'f', 'response' => CHtml::errorSummary($model)));
                Yii::app()->end();
            }

        } else {
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
        }
    }

    /**
     * Lists all models.
     */
    public function actionIndex() {
        $dataProvider = new CActiveDataProvider('Event');
        $this->render('index', array(
            'dataProvider' => $dataProvider,
        ));
    }

    /**
     * Manages all models.
     */
    public function actionAdmin() {
        $model = new Event('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['Event']))
            $model->attributes = $_GET['Event'];

        $this->render('admin', array(
            'model' => $model,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id) {
        $model = Event::model()->findByPk((int) $id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param CModel the model to be validated
     */
    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'event-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

}
