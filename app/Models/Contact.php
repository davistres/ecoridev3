<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    /** TODO: faire des dockblocks!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
     *
     * A RETENIR!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
     * Ici j'ai fait un modèle Eloquant
     * => En Laravel, un modèle eloquant est une classe PHP qui représente une table de notre BD
     *
     * La class "CONTACT" a hérité de la classe "Model" de Laravel...
     */
    use HasFactory;
    /**...Ce qui lui permet d'utiliser des fontionnalités de Laravel comme des FACTORY
     *
     * A RETENIR!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
     * Les FACTORY sont des outils de développement qui permettent de générer facilement de fausses données pour remplir votre base de données => pour faaire des tests!
     */


    protected $table = 'contact';
    /** On associe ce modèle à la table "contact" de la base de données.
     * OBLIGATOIRE car sinon, Larael utilisera par défaut la table "contacts".
     */


    protected $primaryKey = 'contact_id';
    /** On dit à Laravel que la colonne qui sert de clé primaire est "contact_id".
     * OBLIGATOIRE sinon, Laravel utilisera par défaut une clé primaire qui se nommra "id".
     */




    public $timestamps = false;
    /**OBLIGATOIRE sinon, Laravel va utiliser 2 colonnes "created_at" et "updated_at" alors qu'elles n'y sont pas dans ma table "contact"*/


    protected $fillable = [
        'nom',
        'mail',
        'sujet',
        'message',
        'date_envoi',
    ];
    /** C'est une protection pour que seules ses colonnes soient remplies lors de la création d'un nouveau contact.*/
}

/** https://grafikart.fr/tutoriels/orm-eloquent-laravel-2115
 * https://www.youtube.com/watch?v=0LCAS5WXnL4
 * https://www.vincent-pieplu.com/cours/conventions-nommage-laravel
 * https://laravel.com/docs/12.x/eloquent
 *
 */