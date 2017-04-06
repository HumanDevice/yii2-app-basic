<?php

namespace app\models;

use app\components\db\ActiveRecord;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property int $id
 * @property string $email
 * @property string $username
 * @property string $password_hash
 * @property string $auth_key
 * @property string $token_activate
 * @property string $token_password
 * @property string $token_email
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 */
class UserRepository extends ActiveRecord implements yii\web\IdentityInterface
{
    const STATUS_ACTIVE = 1;
    const STATUS_NONACTIVE = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [TimestampBehavior::class];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'status', 'username'], 'required'],
            ['status', 'default', 'value' => self::STATUS_NONACTIVE],
            ['status', 'in', 'range' => array_keys(static::statusesList())],
            [['email', 'username'], 'string', 'max' => 255],
            ['email', 'email'],
            [['email', 'username'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'email' => Yii::t('app', 'Email'),
            'username' => Yii::t('app', 'Name'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * Returns list of statuses.
     * @return array
     */
    public static function statusesList()
    {
        return [
            self::STATUS_ACTIVE => Yii::t('app', 'Active'),
            self::STATUS_NONACTIVE => Yii::t('app', 'Non Active'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->primaryKey;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * Returns user based on his email.
     * @param string $email
     * @return static
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email]);
    }

    /**
     * Returns user based on his username.
     * @param string $username
     * @return static
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * Returns user based on activate token.
     * @param string $token
     * @return static
     */
    public static function findByActivateToken($token)
    {
        return static::findOne(['token_activate' => $token]);
    }

    /**
     * Returns user based on password token.
     * @param string $token
     * @return static
     */
    public static function findByPasswordToken($token)
    {
        return static::findOne(['token_password' => $token]);
    }

    /**
     * Returns user based on email token.
     * @param string $token
     * @return static
     */
    public static function findByEmailToken($token)
    {
        return static::findOne(['token_email' => $token]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException;
    }

    /**
     * Validates password.
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model.
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key.
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Generates password reset token.
     */
    public function generatePasswordToken()
    {
        $this->token_password = Yii::$app->security->generateRandomString(20) . '_' . time();
    }

    /**
     * Generates activation token.
     */
    public function generateActivateToken()
    {
        $this->token_activate = Yii::$app->security->generateRandomString(20) . '_' . time();
    }

    /**
     * Generates email token.
     */
    public function generateAEmailToken()
    {
        $this->token_email = Yii::$app->security->generateRandomString(20) . '_' . time();
    }
}
