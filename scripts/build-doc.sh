cd $(dirname $0)/..
BASE_PATH=$(pwd)
YII2_PATH=$BASE_PATH/data/yii-2.0
YII2_DOC_PATH=$BASE_PATH/data/doc-2.0

cd $YII2_PATH
#git pull
cd $BASE_PATH

rm -rf $YII2_DOC_PATH

./yii guide $YII2_PATH/docs/guide data/guide-2.0/en --interactive=0
#./yii api   $YII2_PATH            data/doc-2.0 --interactive=0
#./yii guide $YII2_PATH/docs/guide data/doc-2.0 --interactive=0
