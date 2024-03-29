<?php

namespace backend\controllers;

use Yii;
use common\models\Box;
use common\models\Queue;
use common\models\SaleRecord;
use backend\models\BoxSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\Log;
use yii\data\BaseDataProvider;
use yii\web\MethodNotAllowedHttpException;

// BoxController implements the CRUD actions for Box model.
class BoxController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions'   => ['index', 'view', 'update', 'open-all-box', 'open-box'],
                        'allow'     => true,
                        'roles'     => ['staff'],
                    ],
                    [
                        'actions'   => ['create', 'delete'],
                        'allow'     => true,
                        'roles'     => ['supervisor'],
                    ],
                ],
            ],

            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Function for manual open box
     * @param integer $id
     * @return mixed
     */
    public function actionOpenBox($id)
    {
        $salerecord_model = SaleRecord::find()->where(['id' => $id])->one();
        $model  = Box::find()->where([
            'id' => $salerecord_model->box_id
        ])->one();

        Queue::push($model->store_id, $model->hardware_id);

        Log::push(
            Yii::$app->user->identity->id,
            'box',
            'open',
            [
                'store_id' => $model->store_id,
                'store_name' => $model->store->name,
                'box_code' => $model->code,
            ]
        );

        Yii::$app->session->setFlash('success', 'Please wait.');

        return $this->redirect([
            'sale-record/view', 'id' => $id
        ]);
    }

    /**
     * Function for Open All box
     * @param integer $id
     * @return mixed
     */
    public function actionOpenAllBox($id)
    {
        Queue::push($id, '00OK');

        Log::push(
            Yii::$app->user->identity->id,
            'box',
            'open_all',
            [
                'store_id' => $id
            ]
        );

        Yii::$app->session->setFlash('success', 'Please wait.');

        return $this->redirect(
            [
                'store/view', 'id' => $id
            ]
        );
    }

    /**
     * Lists all Box models. @return mixed
     */
    public function actionIndex()
    {
        $searchModel  = new BoxSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'     => $searchModel,
            'dataProvider'    => $dataProvider,
        ]);
    }

    /**
     * Displays a single Box model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Box model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id)
    {
        $model = new Box();
        $model->store_id = $id;
        $model->code = (Box::find()->where(['store_id' => $id])->count()) + 1;

        if ($model->store->prefix) {
            $model->prefix = $model->store->prefix;
        } else {
            $model->prefix = '(prefix_not_set)';
        }

        if ($model->load(Yii::$app->request->post())) {
            $box_model = Box::find()->where([
                'hardware_id' => $model->hardware_id,
                'store_id' => $model->store_id
            ])->one();

            if ($box_model || $model->hardware_id == '00OK') {
                Yii::$app->session->setFlash('danger', 'hardware_id existed.');

                return $this->render('create', [
                    'model' => $model,
                ]);
            }

            if ($model->save()) {
                return $this->redirect([
                    'store/view',
                    'id' => $model->store_id
                ]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Box model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->code = Box::find()->where([
            'id' => $id
        ])->one()->code;

        if ($model->store->prefix) {
            $model->prefix = $model->store->prefix;
        } else {
            $model->prefix = '(prefix_not_set)';
        }

        if ($model->load(Yii::$app->request->post())) {
            $box_model = Box::find()->where([
                'hardware_id' => $model->hardware_id,
                'store_id' => $model->store_id
            ])->one();

            if ($box_model || $model->hardware_id == '00OK') {
                Yii::$app->session->setFlash('danger', 'hardware_id existed.');

                return $this->render('update', [
                    'model' => $model,
                ]);
            } else {
                if ($model->save()) {
                    return $this->redirect([
                        'store/view',
                        'id' => $model->store_id
                    ]);
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Box model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect([
            'index'
        ]);
    }

    /**
     * Finds the Box model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Box the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Box::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
