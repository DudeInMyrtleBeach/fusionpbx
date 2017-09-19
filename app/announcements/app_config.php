<?php

	//application details
		$apps[$x]['name'] = "Announcement";
		$apps[$x]['uuid'] = "b1a70f85-6b42-4b9b-8c5a-70c8b02b7d14";
		$apps[$x]['category'] = "";
		$apps[$x]['subcategory'] = "";
		$apps[$x]['version'] = "";
		$apps[$x]['license'] = "Mozilla Public License 1.1";
		$apps[$x]['url'] = "http://www.fusionpbx.com";
		$apps[$x]['description']['en-us'] = "";
		$apps[$x]['description']['es-cl'] = "";
		$apps[$x]['description']['de-de'] = "";
		$apps[$x]['description']['de-ch'] = "";
		$apps[$x]['description']['de-at'] = "";
		$apps[$x]['description']['fr-fr'] = "";
		$apps[$x]['description']['fr-ca'] = "";
		$apps[$x]['description']['fr-ch'] = "";
		$apps[$x]['description']['pt-pt'] = "";
		$apps[$x]['description']['pt-br'] = "";

	//destination details
		$y = 0;
		$apps[$x]['destinations'][$y]['type'] = "sql";
		$apps[$x]['destinations'][$y]['label'] = "announcements";
		$apps[$x]['destinations'][$y]['name'] = "announcements";
		$apps[$x]['destinations'][$y]['where'] = "where domain_uuid = '\${domain_uuid}' ";
		$apps[$x]['destinations'][$y]['order_by'] = "announcement_name asc";
		$apps[$x]['destinations'][$y]['field']['context'] = "announcement_context";
		$apps[$x]['destinations'][$y]['field']['name'] = "announcement_name";
		$apps[$x]['destinations'][$y]['field']['destination'] = "announcement_extension";
		$apps[$x]['destinations'][$y]['select_value']['dialplan'] = "transfer:\${destination} XML \${context}";
		$apps[$x]['destinations'][$y]['select_value']['ivr'] = "menu-exec-app:transfer \${destination} XML \${context}";
		$apps[$x]['destinations'][$y]['select_label'] = "\${destination} \${name}";

	//permission details
		$y = 0;
		$apps[$x]['permissions'][$y]['name'] = "announcement_view";
		$apps[$x]['permissions'][$y]['menu']['uuid'] = "b0939384-7055-44e8-8b4c-9f72293e1878";
		$apps[$x]['permissions'][$y]['groups'][] = "superadmin";
		$apps[$x]['permissions'][$y]['groups'][] = "admin";
		$y++;
		$apps[$x]['permissions'][$y]['name'] = "announcement_add";
		$apps[$x]['permissions'][$y]['groups'][] = "superadmin";
		$apps[$x]['permissions'][$y]['groups'][] = "admin";
		$y++;
		$apps[$x]['permissions'][$y]['name'] = "announcement_edit";
		$apps[$x]['permissions'][$y]['groups'][] = "superadmin";
		$apps[$x]['permissions'][$y]['groups'][] = "admin";
		$y++;
		$apps[$x]['permissions'][$y]['name'] = "announcement_delete";
		$apps[$x]['permissions'][$y]['groups'][] = "superadmin";
		$apps[$x]['permissions'][$y]['groups'][] = "admin";
		$y++;

	//schema details
		$y = 0; //table array index
		$z = 0; //field array index
		$apps[$x]['db'][$y]['table'] = "v_announcements";
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "domain_uuid";
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = "uuid";
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = "char(36)";
		$apps[$x]['db'][$y]['fields'][$z]['key']['type'] = "foreign";
		$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['table'] = "v_domains";
		$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['field'] = "domain_uuid";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "announcement_uuid";
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = "uuid";
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = "char(36)";
		$apps[$x]['db'][$y]['fields'][$z]['key']['type'] = "primary";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "dialplan_uuid";
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = "uuid";
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = "char(36)";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en'] = "";
		$apps[$x]['db'][$y]['fields'][$z]['key']['type'] = "foreign";
		$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['table'] = "v_dialplans";
		$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['field'] = "dialplan_uuid";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "announcement_name";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en'] = "Enter the name.";
		$apps[$x]['db'][$y]['fields'][$z]['description']['pt-br'] = "Entre com o nome.";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "announcement_extension";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en'] = "Enter the extension number.";
		$apps[$x]['db'][$y]['fields'][$z]['description']['pt-br'] = "Entre com o ramal.";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "announcement_greeting";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en'] = "Announcement greeting";
		$apps[$x]['db'][$y]['fields'][$z]['description']['pt-br'] = "Entre com o código de recurso.";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "announcement_context";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en'] = "Enter the context.";
		$apps[$x]['db'][$y]['fields'][$z]['description']['pt-br'] = "Entre com o contexto.";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "announcement_description";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en'] = "Enter the description.";
		$apps[$x]['db'][$y]['fields'][$z]['description']['pt-br'] = "Entre com a descrição.";

?>
