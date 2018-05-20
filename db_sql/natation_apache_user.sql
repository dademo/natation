CREATE USER apache WITH PASSWORD 'apache';

GRANT EXECUTE ON FUNCTION public.armor(bytea) TO apache;

GRANT EXECUTE ON FUNCTION public.armor(bytea, text[], text[]) TO apache;

GRANT EXECUTE ON FUNCTION public.checkvalue_different(tocheck anyelement, comparison anyelement, onexceptiontext character varying) TO apache;

GRANT EXECUTE ON FUNCTION public.crypt(text, text) TO apache;

GRANT EXECUTE ON FUNCTION public.dearmor(text) TO apache;

GRANT EXECUTE ON FUNCTION public.decrypt(bytea, bytea, text) TO apache;

GRANT EXECUTE ON FUNCTION public.decrypt_iv(bytea, bytea, bytea, text) TO apache;

GRANT EXECUTE ON FUNCTION public.digest(text, text) TO apache;

GRANT EXECUTE ON FUNCTION public.digest(bytea, text) TO apache;

GRANT EXECUTE ON FUNCTION public.encrypt(bytea, bytea, text) TO apache;

GRANT EXECUTE ON FUNCTION public.encrypt_iv(bytea, bytea, bytea, text) TO apache;

GRANT EXECUTE ON FUNCTION public.gen_random_bytes(integer) TO apache;

GRANT EXECUTE ON FUNCTION public.gen_random_uuid() TO apache;

GRANT EXECUTE ON FUNCTION public.gen_salt(text, integer) TO apache;

GRANT EXECUTE ON FUNCTION public.gen_salt(text) TO apache;

GRANT EXECUTE ON FUNCTION public.getequipeclub(idequipe integer) TO apache;

GRANT EXECUTE ON FUNCTION public.getjugescompetition(idcompetition integer) TO apache;

GRANT EXECUTE ON FUNCTION public.getjugesequipe(idequipe integer) TO apache;

GRANT EXECUTE ON FUNCTION public.hmac(bytea, bytea, text) TO apache;

GRANT EXECUTE ON FUNCTION public.hmac(text, text, text) TO apache;

GRANT EXECUTE ON FUNCTION public.login(adressemail character varying, motdepasse character varying) TO apache;

GRANT EXECUTE ON FUNCTION public.pgp_armor_headers(text, OUT key text, OUT value text) TO apache;

GRANT EXECUTE ON FUNCTION public.pgp_key_id(bytea) TO apache;

GRANT EXECUTE ON FUNCTION public.pgp_pub_decrypt(bytea, bytea, text) TO apache;

GRANT EXECUTE ON FUNCTION public.pgp_pub_decrypt(bytea, bytea, text, text) TO apache;

GRANT EXECUTE ON FUNCTION public.pgp_pub_decrypt(bytea, bytea) TO apache;

GRANT EXECUTE ON FUNCTION public.pgp_pub_decrypt_bytea(bytea, bytea, text, text) TO apache;

GRANT EXECUTE ON FUNCTION public.pgp_pub_decrypt_bytea(bytea, bytea) TO apache;

GRANT EXECUTE ON FUNCTION public.pgp_pub_decrypt_bytea(bytea, bytea, text) TO apache;

GRANT EXECUTE ON FUNCTION public.pgp_pub_encrypt(text, bytea, text) TO apache;

GRANT EXECUTE ON FUNCTION public.pgp_pub_encrypt(text, bytea) TO apache;

GRANT EXECUTE ON FUNCTION public.pgp_pub_encrypt_bytea(bytea, bytea) TO apache;

GRANT EXECUTE ON FUNCTION public.pgp_pub_encrypt_bytea(bytea, bytea, text) TO apache;

GRANT EXECUTE ON FUNCTION public.pgp_sym_decrypt(bytea, text, text) TO apache;

GRANT EXECUTE ON FUNCTION public.pgp_sym_decrypt(bytea, text) TO apache;

GRANT EXECUTE ON FUNCTION public.pgp_sym_decrypt_bytea(bytea, text, text) TO apache;

GRANT EXECUTE ON FUNCTION public.pgp_sym_decrypt_bytea(bytea, text) TO apache;

GRANT EXECUTE ON FUNCTION public.pgp_sym_encrypt(text, text) TO apache;

GRANT EXECUTE ON FUNCTION public.pgp_sym_encrypt(text, text, text) TO apache;

GRANT EXECUTE ON FUNCTION public.pgp_sym_encrypt_bytea(bytea, text) TO apache;

GRANT EXECUTE ON FUNCTION public.pgp_sym_encrypt_bytea(bytea, text, text) TO apache;

GRANT EXECUTE ON FUNCTION public.nothing() TO apache;

GRANT EXECUTE ON FUNCTION public.read_only() TO apache;

GRANT EXECUTE ON FUNCTION public.trig_fct_club_personne_afterinsertupdate_inscription() TO apache;

GRANT EXECUTE ON FUNCTION public.trig_fct_competition_beforeinsertupdate_ville() TO apache;

GRANT EXECUTE ON FUNCTION public.trig_fct_equipe_afterinsert_newequipe() TO apache;

GRANT EXECUTE ON FUNCTION public.trig_fct_equipe_afterupdate_debutballet() TO apache;

GRANT EXECUTE ON FUNCTION public.trig_fct_equipe_afterupdate_visionnable() TO apache;

GRANT EXECUTE ON FUNCTION public.trig_fct_equipe_jugecompetition_afterinsert_notejurycompetition() TO apache;

GRANT EXECUTE ON FUNCTION public.trig_fct_equipe_jugecompetition_afterinsertupdate_juges() TO apache;

GRANT EXECUTE ON FUNCTION public.trig_fct_equipe_jugecompetition_beforeupdate_notejurycompetitio() TO apache;

GRANT EXECUTE ON FUNCTION public.trig_fct_equipe_personne_afterinsertupdate_personneinscription() TO apache;

GRANT EXECUTE ON FUNCTION public.trig_fct_jugecompetition_afterinsertupdate_estjuge() TO apache;

GRANT EXECUTE ON FUNCTION public.trig_fct_personne_beforeinsertupdate_nomprenom() TO apache;

GRANT EXECUTE ON FUNCTION public.trig_fct_utilisateur_beforeinsertupdate_modifmdp() TO apache;

GRANT ALL ON SEQUENCE public.seq_club_id TO apache;

GRANT ALL ON SEQUENCE public.seq_competition_id TO apache;

GRANT ALL ON SEQUENCE public.seq_equipe_id TO apache;

GRANT ALL ON SEQUENCE public.seq_jugecompetition_id TO apache;

GRANT ALL ON SEQUENCE public.seq_personne_id TO apache;

GRANT ALL ON SEQUENCE public.seq_typejuge_id TO apache;

GRANT ALL ON SEQUENCE public.seq_typeutilisateur_id TO apache;

GRANT ALL ON SEQUENCE public.seq_utilisateur_id TO apache;

GRANT ALL ON TABLE public.club TO apache;

GRANT ALL ON TABLE public.club_personne TO apache;

GRANT ALL ON TABLE public.competition TO apache;

GRANT ALL ON TABLE public.equipe TO apache;

GRANT ALL ON TABLE public.equipe_jugecompetition TO apache;

GRANT ALL ON TABLE public.equipe_personne TO apache;

GRANT ALL ON TABLE public.jugecompetition TO apache;

GRANT ALL ON TABLE public.personne TO apache;

GRANT ALL ON TABLE public.typejuge TO apache;

GRANT ALL ON TABLE public.typeutilisateur TO apache;

GRANT ALL ON TABLE public.utilisateur TO apache;

GRANT ALL ON TABLE public.utilisateur_typeutilisateur TO apache;

GRANT ALL ON TABLE public.all_equipe TO apache;

GRANT ALL ON TABLE public.all_equipe_agg TO apache;

GRANT ALL ON TABLE public.all_juge_competition_notes TO apache;

GRANT ALL ON TABLE public.all_nageur_club TO apache;

GRANT ALL ON TABLE public.all_personne TO apache;

GRANT ALL ON TABLE public.juge_competition TO apache;

GRANT ALL ON TABLE public.juge_competition_agg TO apache;
