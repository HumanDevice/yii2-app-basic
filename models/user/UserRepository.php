<?php

namespace app\models;

use DateTime;
use DateTimeZone;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\helpers\Html;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property int $id
 * @property string $email
 * @property string $username
 * @property string $password_hash
 * @property string $auth_key
 * @property int $status
 * @property int $role
 * @property string $country
 * @property string $timezone
 * @property string $date_format
 * @property string $password_reset
 * @property int $company_id
 * @property int $created_at
 * @property int $updated_at
 *
 * @property string $name
 * @property Avatar $avatar
 * @property string $avatarImage
 * @property Company $company
 */
class UserRepository extends app\components\db\ActiveRecord implements yii\web\IdentityInterface
{
    /**
     * Statuses.
     */
    const STATUS_REMOVED = 0;
    const STATUS_ACTIVE = 1;
    /**
     * Roles.
     */
    const ROLE_ADMIN = 1;
    const ROLE_SALE = 2;
    const ROLE_MANAGER = 3;
    const ROLE_OPERATOR = 4;

    /**
     * @var string new password
     */
    public $new_password;

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
        return [
            TimestampBehavior::className()
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'status', 'role', 'date_format', 'timezone', 'country'], 'required'],
            ['new_password', 'required', 'on' => 'new_user'],
            ['status', 'in', 'range' => array_keys(static::statusesList())],
            ['role', 'checkRoleType', 'except' => 'console'],
            [['email', 'username'], 'string', 'max' => 255],
            ['email', 'email'],
            ['email', 'unique'],
            ['new_password', 'checkPassword'],
            ['date_format', 'in', 'range' => array_keys(static::dateFormatsList())],
            ['timezone', 'in', 'range' => array_keys(static::timezonesList())],
            ['company_id', 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['company_id' => 'id']],
            ['country', 'in', 'range' => array_keys(Country::all())],
            ['company_id', 'checkRole', 'skipOnEmpty' => false],
        ];
    }

    /**
     * Checks if role is correct.
     */
    public function checkRoleType()
    {
        if ($this->role != $this->getOldAttribute('role')) {
            if (Yii::$app->user->role == self::ROLE_ADMIN && !in_array($this->role, array_keys(static::rolesList()))) {
                $this->addError('role', Yii::t('app', 'Role is invalid.'));
            } elseif (Yii::$app->user->role != self::ROLE_ADMIN && !in_array($this->role, array_keys(Company::rolesList()))) {
                $this->addError('role', Yii::t('app', 'Role is invalid.'));
            }
        }
    }

    /**
     * Checks if password is correct.
     */
    public function checkPassword()
    {
        if (!preg_match('/^(?=.*\p{Lu})(?=.*\p{Ll})(?=.*\d).{8,255}$/u', $this->new_password)) {
            $this->addError('new_password', Yii::t('app', 'Password must contain lower and upper case letter and a digit (min. 8 characters)'));
        }
    }

    /**
     * Checks if company is of proper type for the role.
     */
    public function checkRole()
    {
        if (!empty($this->company_id)) {
            if ($this->role == self::ROLE_ADMIN) {
                $this->addError('company_id', Yii::t('app', 'Admin role can not be assigned to a company.'));
            } else {
                $companyType = Company::typeOf($this->company_id);
                if ($this->role == self::ROLE_SALE && $companyType != Company::TYPE_SALE) {
                    $this->addError('company_id', Yii::t('app', 'Sale role can be assigned only to a Sale type company.'));
                } elseif ($this->role != self::ROLE_SALE && $companyType == Company::TYPE_SALE) {
                    $this->addError('company_id', Yii::t('app', 'Non Sale roles can not be assigned to a Sale type company.'));
                }
            }
        } elseif (empty($this->company_id) && $this->role != self::ROLE_ADMIN) {
            $this->addError('company_id', Yii::t('app', 'Company is required for non Admin roles.'));
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'email' => Yii::t('app', 'Email'),
            'username' => Yii::t('app', 'Displayed name'),
            'status' => Yii::t('app', 'Status'),
            'role' => Yii::t('app', 'Role'),
            'country' => Yii::t('app', 'Country'),
            'timezone' => Yii::t('app', 'Timezone'),
            'new_password' => Yii::t('app', 'New password'),
            'date_format' => Yii::t('app', 'Date format'),
            'created_at' => Yii::t('app', 'Created at'),
            'updated_at' => Yii::t('app', 'Updated at'),
            'company_id' => Yii::t('app', 'Company'),
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
            self::STATUS_REMOVED => Yii::t('app', 'Removed'),
        ];
    }

    /**
     * Returns list of roles.
     * @return array
     */
    public static function rolesList()
    {
        return [
            self::ROLE_ADMIN => Yii::t('app', 'Admin'),
            self::ROLE_SALE => Yii::t('app', 'Sale'),
            self::ROLE_MANAGER => Yii::t('app', 'Manager'),
            self::ROLE_OPERATOR => Yii::t('app', 'Operator'),
        ];
    }

    /**
     * Returns list of date formats.
     * @return array
     */
    public static function dateFormatsList()
    {
        return [
            'yyyy-MM-dd' => Yii::t('app', 'YYYY-MM-DD'),
            'yyyy-dd-MM' => Yii::t('app', 'YYYY-DD-MM'),
            'dd-MM-yyyy' => Yii::t('app', 'DD-MM-YYYY'),
            'MM-dd-yyyy' => Yii::t('app', 'MM-DD-YYYY'),
        ];
    }

    /**
     * Returns timezones with current offset array.
     * @return array timezones
     */
    public static function timezonesList()
    {
        $timeZones = [];

        $timezone_identifiers = DateTimeZone::listIdentifiers();
        sort($timezone_identifiers);

        $timeZones['UTC'] = Yii::t('app', 'default (UTC)');

        foreach ($timezone_identifiers as $zone) {
            if ($zone != 'UTC') {
                $zoneName = $zone;
                $timeForZone = new DateTime(null, new DateTimeZone($zone));
                $offset = $timeForZone->getOffset();
                if (is_numeric($offset)) {
                    $zoneName .= ' (UTC';
                    if ($offset != 0) {
                        $offset = $offset / 60 / 60;
                        $offsetDisplay = floor($offset) . ':' . str_pad(60 * ($offset - floor($offset)), 2, '0', STR_PAD_LEFT);
                        $zoneName .= ' ' . ($offset < 0 ? '' : '+') . $offsetDisplay;
                    }
                    $zoneName .= ')';
                }
                $timeZones[$zone] = $zoneName;
            }
        }
        return $timeZones;
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
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Returns user based on his email.
     * @param string $email
     * @return User
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Returns user based on reset token.
     * @param string $token
     * @return User
     */
    public static function findByResetToken($token)
    {
        return static::findOne(['password_reset' => $token, 'status' => self::STATUS_ACTIVE]);
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
     * @return boolean if password provided is valid for current user
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
     * Returns name of user or his email.
     * @return string
     */
    public function getName()
    {
        return !empty($this->username) ? $this->username : $this->email;
    }

    /**
     * @return ActiveQuery
     */
    public function getAvatar()
    {
        return $this->hasOne(Avatar::className(), ['user_id' => 'id']);
    }

    /**
     * Returns avatar source.
     * @return string
     */
    public function getAvatarImage()
    {
        if ($this->avatar) {
            return $this->avatar->source;
        }
        return 'data:image/png;base64,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';
    }

    /**
     * @return ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::className(), ['id' => 'company_id']);
    }

    /**
     * Returns role label.
     * @param int $role
     * @return string
     */
    public static function roleLabel($role)
    {
        if (!in_array($role, array_keys(static::rolesList()))) {
            return null;
        }
        $colors = [
            self::ROLE_ADMIN => 'danger',
            self::ROLE_SALE => 'warning',
            self::ROLE_MANAGER => 'primary',
            self::ROLE_OPERATOR => 'success',
        ];
        return Html::tag('span', static::rolesList()[$role], ['class' => 'label label-' . $colors[$role]]);
    }

    /**
     * Returns box class per role.
     * @param int $role
     * @return string
     */
    public static function boxColor($role)
    {
        if (!in_array($role, array_keys(static::rolesList()))) {
            return null;
        }
        $colors = [
            self::ROLE_ADMIN => 'bg-red-gradient',
            self::ROLE_SALE => 'bg-yellow-gradient',
            self::ROLE_MANAGER => 'bg-blue-gradient',
            self::ROLE_OPERATOR => 'bg-green-gradient',
        ];
        return $colors[$role];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (!empty($this->new_password)) {
                $this->generateAuthKey();
                $this->setPassword($this->new_password);
            }
            return true;
        }
        return false;
    }

    /**
     * Generates password reset token.
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset = Yii::$app->security->generateRandomString(20) . '_' . time();
    }
}