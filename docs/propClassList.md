
| rfc8984<br>Property Name |  rfc8984<br>Property Type |  rfc8984<br>Property Context |  rfc8984<br>Section |  iCal (rfc5545)<br>Property Name |  iCal<br>Component |  Comment | 
| --- |  --- |  --- |  --- |  --- |  --- |  --- | 
||||||||
|acknowledged | **DateTime**(UTC) | Alert | 4.5.2 | acknowledged |  Valarm|
||||||||
|action | string | Alert | 4.5.2 | action|Valarm|||
||||||||
|alerts | id\[**Alert**] | Event|4.5.2 |  | Vevent/Valarm|
|alerts | id\[**Alert**] | Task|4.5.2 |  | Vtodo/Valarm
||||||||
|aliases | string\[boolean] | TimeZone | 4.7.2 | tzaliasof|Vtimezone|
||||||||
|byDay | **NDay**\[] | RecurrenceRule | 4.3.3 | rrule/exrule \[ BYDAY ]||Vevent<br>Vtodo<br>Standard<br>Daylight|
||||||||
||||||||
|byHour | unsignedint\[] | RecurrenceRule | 4.3.3 | rrule/exrule \[ BYHOUR ]||Vevent<br>Vtodo<br>Standard<br>Daylight|
||||||||
|byMinute | unsignedint\[] | RecurrenceRule | 4.3.3 | rrule/exrule \[ BYMINUTE ]||Vevent<br>Vtodo<br>Standard<br>Daylight|
||||||||
|byMonth | string\[] | RecurrenceRule | 4.3.3 | rrule/exrule \[ BYMONTH ]||Vevent<br>Vtodo<br>Standard<br>Daylight|
||||||||
|byMonthDay | int\[] | RecurrenceRule | 4.3.3 | rrule/exrule \[ BYMONTHDAY ]||Vevent<br>Vtodo<br>Standard<br>Daylight|
||||||||
|bySecond | unsignedint\[] | RecurrenceRule | 4.3.3 | rrule/exrule \[ BYSECOND ]||Vevent<br>Vtodo<br>Standard<br>Daylight|
||||||||
|bySetPosition | int\[] | RecurrenceRule | 4.3.3 | rrule/exrule \[ BYSETPOS ]||Vevent<br>Vtodo<br>Standard<br>Daylight|
||||||||
|byWeekNo | int\[] | RecurrenceRule | 4.3.3 | rrule/exrule \[ BYWEEKNO ]||Vevent<br>Vtodo<br>Standard<br>Daylight|
||||||||||
|byYearDay | int\[] | RecurrenceRule | 4.3.3 | rrule/exrule \[ BYYEARDAY ]||Vevent<br>Vtodo<br>Standard<br>Daylight|
||||||||
|categories | string\[boolean] | Event|4.2.10 |categories|Vevent|
|categories | string\[boolean] | Task|4.2.10 |categories|Vtodo|
|categories | string\[boolean] | Group | 4.2.10 | |||
||||||||
|cid | string | Link | 1.4.11 | | |(url/X-URL) X-CID|
||||||||
|color | string | Event|4.2.11 | color|Vevent|
|color | string | Task|4.2.11 | color|Vtodo|
|color | string | Group | 4.2.11 | color||Vcalendar|
||||||||
|comments | string\[] | TimeZoneRule | 4.7.2 | comment||Standard<br>Daylight"|
||||||||
|contentType | string | Link | 1.4.11 | url X-URL-<num>|Vcalendar<br>Vevent<br>Vtodo|X-CONTENTTYPE|
||||||||
|coordinates | string | Location | 4.2.5 | geo / url||Vlocation|
||||||||
|count | unsignedint | RecurrenceRule | 4.3.3 | rrule/exrule \[ COUNT ]||Vevent<br>Vtodo<br>Standard<br>Daylight"|||
||||||||
|created | **DateTime**(UTC) | Event|4.1.5 | created | Vevent|
|created | **DateTime**(UTC) | Task|4.1.5 | created | Vevent|
|created | **DateTime**(UTC) | Group | 4.1.5 | created |Vcalendar|
||||||||
|day | string | NDay | 4.3.3 | rrule/exrule \[ BYDAY ]||Vevent<br>Vtodo<br>Standard<br>Daylight|
||||||||
|daylight | **TimeZoneRule**\[] | TimeZone | 4.7.2 |  | Daylight|
||||||||
|delegatedFrom | id\[boolean] | Participant | 4.4.6 |  |  | Attendee:DELEGATEDFROM|
||||||||
|delegatedTo | id\[boolean] | Participant | 4.4.6 |  |  | Attendee:DELEGATEDTO|
||||||||
|description | string | Event | 4.2.2, | description | Vevent|
|description | string | Task | 4.2.2, | description | Vtodo|
|description | string | Location, | 4.2.5, | description | Vlocation|
|description | string | Participant, | 4.4.6, | description | Particpant|
|description | string | VirtualLocation | 4.2.6 | ||||
||||||||
|descriptionContentType|string | Event|4.2.3 | description | Vevent|X-descriptionContentType|
|descriptionContentType|string | Task|4.2.3 | description | Vtodo|X-descriptionContentType|
||||||||
|display | string | Link | 1.4.11 | url X-URL-<num>|Vcalendar<br>Vevent<br>Vtodo"|X-DISPLAY||
||||||||
|due | **DateTime**(local) | Task | 5.2.1 | due||Vtodo|
||||||||
|duration | **Duration** | Event | 5.1.2 | duration|Vevent|if start exists (computed) dtend|
||||||||
|email | string | Participant | 4.4.6 | calendaraddress||Particpant|
||||||||
|entries | **Task**/**Event**\[]|Group|5.3.1 | |||
||||||||
|estimatedDuration | **Duration** | Task | 5.2.3 | duration|Vtodo|if due exists due param X-ESTIMATEDDURATION|
||||||||
|excluded | boolean | Event|4.3.6 | X-EXCLUDED||Vevent|
|excluded | boolean | Task|4.3.6 | X-EXCLUDED||Vtodo|
||||||||
|excludedRecurrenceRules | **RecurrenceRule**\[] | Event|4.3.4 | exrule||Vevent|
|excludedRecurrenceRules | **RecurrenceRule**\[] | Task|4.3.4 | exrule||Vtodo|||
||||||||
|expectReply | boolean | Participant | 4.4.6 |  |  | Attendee:RSVP||
||||||||
|features | string\[boolean] | VirtualLocation | 4.2.6 | X-\<feature>||Vlocation|||
||||||||
|firstDayOfWeek | string | RecurrenceRule | 4.3.3 | rrule/exrule \[ WKST ]||Vevent<br>Vtodo<br>Standard<br>Daylight"|||
||||||||
||||||||
|freeBusyStatus | string | Event|4.4.2 | X-FREEBUSYSTATUS||Vevent|
|freeBusyStatus | string | Task|4.4.2 | X-FREEBUSYSTATUS||Vtodo|
||||||||
|frequency | string | RecurrenceRule | 4.3.3 | rrule/exrule \[ FREQ ]||Vevent<br>Vtodo<br>Standard<br>Daylight"|
||||||||
|href | string | Link | 1.4.11 | url X-URL-<num>||Vcalendar<br>Vevent<br>Vtodo"|value|
||||||||
|interval | unsignedint | RecurrenceRule | 4.3.3 | rrule/exrule \[ INTERVAL ]|Vevent<br>Vtodo<br>Standard<br>Daylight"|
||||||||
|invitedBy | id | Participant | 4.4.6 | X-INVITEDBY||Particpant<br>>Attendee:X-INVITEDBY|
||||||||
|keywords | string\[boolean] | Event|4.2.9 | X-KEYWORDS||Vevent|comma separated list|
|keywords | string\[boolean] | Task|4.2.9 | X-KEYWORDS||Vtodo|comma separated list|
|keywords | string\[boolean] | Group | 4.2.9 | X-KEYWORDS|Vcalendar|comma separated list|
||||||||
|kind | string | Participant | 4.4.6 | ||Attendee:CUTYPE|
||||||||
|language | string | Participant | 4.4.6 | X-LANGAGE|Particpant|Attendee:LANGUAGE||
||||||||
|links | id\[**Link**] | Group|4.2.7, | Image, url||Vcalendar|
|links | id\[**Link**] | Event|4.2.7, | Image, structured_data, url||Vevent|
|links | id\[**Link**] | Task|4.2.7, | Image, structured_data, url||Vtodo|
|links | id\[**Link**] | Location|4.2.5, | structured_data||Vlocation|
|links | id\[**Link**] | Participant | 4.4.6 | structured_data url||Particpant|Attendee:X-HREF
||||||||
|locale | string | Group|4.2.8 | ||||
|locale | string | Event|4.2.8 |  | Vevent|LANGUAGE|
|locale | string | Task | 4.2.8 | Vtodo|LANGUAGE||
||||||||
|localizations | string\[**PatchObject**]|Event|4.6.1 | X-LOCALIZATIONS-<num>||Vevent|||
|localizations | string\[**PatchObject**]|Task|4.6.1 | X-LOCALIZATIONS-<num>||Vtodo|||
||||||||
|locationid | id | Participant | 4.4.6 |  | Participant|Vlocation||pointer|
||||||||
|locations | id\[**Location**] | Event|4.2.5 |  | Vevent/Vlocations|
|locations | id\[**Location**] | Task|4.2.5 |  | Vtodo/Vlocations|
||||||||
|locationTypes | string\[boolean] | Location | 4.2.5 | loctiontype||Vlocation||comma separated list|
||||||||
||||||||
|memberOf | id\[boolean] | Participant | 4.4.6 |  |  | Attendee:MEMBER|
||||||||
|method | string | Event|4.1.8 | mehod|Vcalendar|
|method | string | Task|4.1.8 | mehod|Vcalendar|
||||||||
|name | string | Location, | 4.2.5, | name|Vlocation|LANGUAGE may have locale
|   |  | ||||||||
|name | string | VirtualLocation,|4.2.6, | name|Vlocation|LANGUAGE may have locale
||||||||
|name | string | Participant | 4.4.6 | summary|Particpant|
||||||||
|names | string\[boolean] | TimeZoneRule | 4.7.2 | tzname|Standard<br>Daylight|
||||||||
|nthOfPeriod | int | NDay | 4.3.3 | rrule/exrule \[ BYDAY ]||Vevent<br>Vtodo<br>Standard<br>Daylight|
||||||||
|offset | **Duration**(signed) | OffsetTrigger | 4.5.2 | trigger|Valarm|
||||||||||
|offsetFrom | **DateTime**(UTC) | TimeZoneRule | 4.7.2 | offsetfrom|Standard<br>Daylight|
||||||||
|offsetTo | **DateTime**(UTC) | TimeZoneRule | 4.7.2 | offsetto|Standard<br>Daylight|
||||||||
|participants | Id\[**Participant**] | Event|4.4.6 |  | Vevent|Particpant|
||||||Attendee|Vevent|
|participants | Id\[**Participant**] | Task|4.4.6 |  | Vtodo|Particpant|
||||||Attendee|Vtodo|
||||||||
|participationComment|string | Participant | 4.4.6 | comment|Particpant|Attendee:X-COMMENT
||||||||
|participationStatus | string | Participant | 4.4.6 | status | Particpant|Attendee:PARTSTAT||
||||||||
|percentComplete | unsignedint | Task, | 5.2.4 | percentComplete | Vtodo|
|percentComplete | unsignedint | Participant | 5.2.4 | X-PERCENTCOMPLETE|Particpant|Attendee:X-PERCENTCOMPLETE
||||||||
|priority | int | Event|4.4.1 | priority | Vevent|
|priority | int | Task | 4.4.1 | priority | Vtodo|
||||||||
|privacy | string | Event|4.4.3 | class|Vevent|
|privacy | string | Task|4.4.3 | class|Vtodo|
||||||||
|progress | string | Task, | 5.2.5 | status|Vtodo|||
|progress | string | Participant | 5.2.5 | X-PROGRESS|Particpant|Attendee:X-PROGRESS
||||||||
|progressUpdated | **DateTime**(UTC) | Task, | 5.2.6 | lastmodified|Vtodo|
|progressUpdated | **DateTime**(UTC) | Participant | 5.2.6 | X-PROGRESSUPDATED|Particpant|Attendee:X-PROGRESSUPDATED
||||||||
|recurrenceId | **DateTime**(local) | Event | 4.3.1 | recurrenceId | Vevent|
|recurrenceId | **DateTime**(local) | Task | 4.3.1 | recurrenceId | Vtodo|
||||||||
|recurrenceIdTimeZone | TimeZoneId | Event | 4.3.2 | recurrenceId | Vevent|
|recurrenceIdTimeZone | TimeZoneId | Task | 4.3.2 | recurrenceId | Vtodo|
||||||||
|recurrenceOverrides | **DateTime**(local)\[**PatchObject**] | Event|4.3.5, | rdate|Vevent|PatchObject keys as x-?||
|recurrenceOverrides | **DateTime**(local)\[**PatchObject**] | Task|4.3.5, | rdate|Vtodo|PatchObject keys as x-?||
|recurrenceOverrides | **DateTime**(local)\[**PatchObject**] | TimeZoneRule | 4.7.2 | rdate|Standard<br>DayLight|PatchObject keys as x-?
||||||||
|recurrenceRules | **RecurrenceRule**\[] | Event|4.3.3, | rrule|Vevent|
|recurrenceRules | **RecurrenceRule**\[] | Task|4.3.3, | rrule|Vtodo|
|recurrenceRules | **RecurrenceRule**\[] | TimeZoneRule | 4.7.2 | rrule||Standard<br>DayLight|
||||||||
|rel | string | Link | 1.4.11 | url X-URL-<num>|Vcalendar<br>Vevent<br>Vtodo|X-REL|
||||||||
|relatedTo | string\[**Relation**] | Event|4.1.3, | relatedto|Vevent|RELTYPE/x-?||
|relatedTo | string\[**Relation**] | Task|4.1.3, | relatedto|Vtodo|RELTYPE/x-?||
|relatedTo | string\[**Relation**] | Alert | 4.5.2 | relatedto|Valarm|RELTYPE/x-?||
||||||||
|relation | string\[boolean] | Relation | 1.4.10 | |Vevent<br>Vtodo<br>Valarm|relatedto::RELATED/X-?
||||||||
|relativeTo | string | OffsetTrigger, | 4.5.2, | ||||||
|relativeTo | string | Location | 4.2.5 | X-RELATIVETO|Vlocation|||
||||||||
|replyTo | string\[string] | Event|4.4.4 | organizer|X-REPYTO-<num>|Vevent|||
|replyTo | string\[string] | Task|4.4.4 | organizer|X-REPYTO-<num>|Vtodo|||
||||||||
|requestStatus | string | Event|4.4.7 | requeststatus|Vevent|
|requestStatus | string | Task|4.4.7 | requeststatus|Vtodo|
||||||||
|roles | string\[boolean] | Participant | 4.4.6 | participanttype|Particpant|Attendee:ROLE<br>>Attendee:X-PARTICPANTTYE|Comma separated list|
||||||||
|rscale | string | RecurrenceRule | 4.3.3 | rrule/exrule \[ RSCALE ]|Vevent<br>Vtodo<br>Standard<br>Daylight"|
||||||||
|sentBy | string | Event|4.4.5, | X-SENTBY|Vevent|
|sentBy | string | Task|4.4.5, | X-SENTBY|Vtodo|
|sentBy | string | Participant | 4.4.6 | ||Attendee:X-SENT-BY
||||||||
|standard | **TimeZoneRule**\[] | TimeZone | 4.7.2 | ||Standard|
||||||||
|start | **DateTime**(local) | TimeZoneRule | 4.7.2 | dtstart|Standard<br>Daylight|
||||||||
|scheduleAgent | string | Participant | 4.4.6 | X-SCHEDULEAGENT|Particpant|
||||||||
|scheduleForceSend | boolean | Participant | 4.4.6 | X-SCHEDULEFORCESEND|Particpant|||
||||||||
|scheduleSequence | unsignedint | Participant | 4.4.6 | X-SCHEDULESEQUENCE|Particpant|||
||||||||
|scheduleStatus | string\[] | Participant | 4.4.6 | requeststatus|Particpant|Attendee:X-SCHEDULESTATUS
||||||||
|scheduleUpdated | **DateTime**(UTC) | Participant | 4.4.6 | X-SCHEDULEUPDATED|Particpant|Attendee:X-SCHEDULEUPDATED
||||||||
|sendTo | string\[string] | Participant | 4.4.6 | Contact|Particpant|
||||||||
|sequence | unsignedint | Event|4.1.7 | sequence|Vevent|
|sequence | unsignedint | Task|4.1.7 | sequence|Vtodo|
||||||||
|showWithoutTime | boolean | Event|4.2.4 | ||dtstart/dtend: VALUE = DATE (if true)|
|showWithoutTime | boolean | Task|4.2.4 | ||dtstart/dtend: VALUE = DATE (if true)|
||||||||
|size | unsignedint | Link | 1.4.11 | url X-URL-\<num>|Vcalendar<br>Vevent<br>Vtodo|X-SIZE
||||||||
|skip | string | RecurrenceRule | 4.3.3 | rrule/exrule \[ SKIP ]|Vevent<br>Vtodo<br>Standard<br>Daylight|
||||||||
|source | string | Group | 5.3.2 | source|Vcalendar|
||||||||
|start | **DateTime**(local) | Event|5.1.1, | dtstart|Vevent|
|start | **DateTime**(local) | Task|5.2.2 | dtstart|Vtodo|
||||||||
|status | string | Event | 5.1.3 | status | Vevent|
||||||||
|timeZone | TimeZoneId | Event|4.7.1, | ||Vtimezone::tzid|
|timeZone | TimeZoneId | Task|4.7.1, | ||Vtimezone::tzid|
|timeZone | TimeZoneId | Location | 4.2.5 | X-TIMEZONE|Vlocation|Vtimezone::tzid|
||||||||
|timeZones | TimeZoneId\[**TimeZone**]|Event|4.7.2 | ||Vtimezone|
|timeZones | TimeZoneId\[**TimeZone**]|Task|4.7.2 | ||Vtimezone|
||||||||
|title | string | Event|4.2.1 | name | Vevent|
|title | string | Task|4.2.1 | summary | Vtodo|
|title | string | Group|4.2.1 | name | Vcalendar|
|title | string | Link|4.2.1 | url X-URL-<num> | Vcalendar<br>Vevent<br>Vtodo|X-TITLE||
||||||||
|trigger | **OffsetTrigger**<br>**AbsoluteTrigger**<br>**UnknownTrigger** | Alert | 4.5.2 | trigger|Valarm|
||||||||
|tzId | string | TimeZone | 4.7.2 | tzid||Vtimezone|
||||||||||
|uid | string | Event|4.1.2 | uid|Vevent|
|uid | string | Task|4.1.2 | uid|Vtodo|
|uid | string | Group | 4.1.2 | uid|Vcalendar|
||||||||
|until | **DateTime**(local) | RecurrenceRule | 4.3.3 | rrule/exrule \[ UNTIL ]|Vevent<br>Vtodo<br>Standard<br>Daylight|||
||||||||
|updated | **DateTime**(UTC) | Event|4.1.6 | lastmodified|Vevent|
|updated | **DateTime**(UTC) | Task|4.1.6 | lastmodified|Vtodo|
|updated | **DateTime**(UTC) | Group | 4.1.6 | lastmodified|Vcalendar|
|updated | **DateTime**(UTC) | TimeZone | 4.7.2 | lastmodified|Vtimezone|
||||||||
|uri | string | VirtualLocation | 4.2.6 | url|Vlocation|
||||||||
|url | string | TimeZone | 4.7.2 | tzurl|Vtimezone|
||||||||
|useDefaultAlerts | boolean | Event|4.5.1 | X-USEDEFAULTALERTS|Vevent|'TRUE’/’FALSE’|
|useDefaultAlerts | boolean | Task|4.5.1 | X-USEDEFAULTALERTS|Vtodo|'TRUE’/’FALSE’|
||||||||
|validUntil | **DateTime**(UTC) | TimeZone | 4.7.2 | tzuntil|Vtimezone|
||||||||
|virtualLocations | id\[**VirtualLocation**]|Event|4.2.6 | |Vevent/Vlocation|X-prop<br>X-VIRTUALLOCATION = 1|
|virtualLocations | id\[**VirtualLocation**]|Task|4.2.6 | |Vtodo/Vlocation|X-prop<br>X-VIRTUALLOCATION = 1|
||||||||
|when | **DateTime**(UTC) | AbsoluteTrigger | 4.5.2 | trigger|Valarm|
||||||||
