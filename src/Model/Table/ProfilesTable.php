<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Profiles Model
 *
 * 
 * @property \App\Model\Table\PetsTable|\Cake\ORM\Association\BelongsTo $Pets
 * @property \App\Model\Table\BreedsTable|\Cake\ORM\Association\BelongsTo $Breeds
 *
 * @method \App\Model\Entity\Profile get($primaryKey, $options = [])
 * @method \App\Model\Entity\Profile newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Profile[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Profile|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Profile patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Profile[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Profile findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ProfilesTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('profiles');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        
        $this->belongsTo('Users', [
            'foreignKey' => 'owner_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Pets', [
            'foreignKey' => 'pet_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Breeds', [
            'foreignKey' => 'breed_id',
            'joinType' => 'INNER'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->scalar('pet_name')
            ->maxLength('pet_name', 200)
            ->requirePresence('pet_name', 'create')
            ->notEmpty('pet_name');

        $validator
            ->integer('size')
            ->requirePresence('size', 'create')
            ->notEmpty('size');

        $validator
            ->boolean('gender')
            ->requirePresence('gender', 'create')
            ->notEmpty('gender');

        $validator
            ->scalar('age')
            ->maxLength('age', 30)
            ->requirePresence('age', 'create')
            ->notEmpty('age');

        $validator
            ->scalar('color')
            ->maxLength('color', 100)
            ->requirePresence('color', 'create')
            ->notEmpty('color');

        $validator
            ->boolean('neutered')
            ->requirePresence('neutered', 'create')
            ->notEmpty('neutered');

        $validator
            ->boolean('intact')
            ->requirePresence('intact', 'create')
            ->notEmpty('intact');

        $validator
            ->scalar('mix')
            ->maxLength('mix', 100)
            ->allowEmpty('mix');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['owner_id'], 'Users'));
        $rules->add($rules->existsIn(['pet_id'], 'Pets'));
        $rules->add($rules->existsIn(['breed_id'], 'Breeds'));

        return $rules;
    }
}
