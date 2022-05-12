
| rfc8984<br>Property Name |  rfc8984<br>Property Type |  rfc8984<br>Property Context |  rfc8984<br>Section |  iCal (rfc5545)<br>Property Name |  iCal<br>Component |  Comment | 
| --- |  --- |  --- |  --- |  --- |  --- |  --- | 
||||||||
|acknowledged | UTCDateTime | Alert | 4.5.2 | acknowledged |  Valarm|
||||||||
|action | String | Alert | 4.5.2 | action|Valarm|||
||||||||
|alerts | Id\[Alert] | Event|4.5.2 |  | Vevent/Valarm|
|alerts | Id\[Alert] | Task|4.5.2 |  | Vtodo/Valarm
||||||||
|aliases | String\[Boolean] | TimeZone | 4.7.2 | tzaliasof|Vtimezone|
||||||||
|byDay | NDay\[] | RecurrenceRule | 4.3.3 | rrule/exrule \[ BYDAY ]||Vevent<br>Vtodo<br>Standard<br>Daylight|
||||||||
||||||||
|byHour | UnsignedInt\[] | RecurrenceRule | 4.3.3 | rrule/exrule \[ BYHOUR ]||Vevent<br>Vtodo<br>Standard<br>Daylight|
||||||||
|byMinute | UnsignedInt\[] | RecurrenceRule | 4.3.3 | rrule/exrule \[ BYMINUTE ]||Vevent<br>Vtodo<br>Standard<br>Daylight|
||||||||
|byMonth | String\[] | RecurrenceRule | 4.3.3 | rrule/exrule \[ BYMONTH ]||Vevent<br>Vtodo<br>Standard<br>Daylight|
||||||||
|byMonthDay | Int\[] | RecurrenceRule | 4.3.3 | rrule/exrule \[ BYMONTHDAY ]||Vevent<br>Vtodo<br>Standard<br>Daylight|
||||||||
|bySecond | UnsignedInt\[] | RecurrenceRule | 4.3.3 | rrule/exrule \[ BYSECOND ]||Vevent<br>Vtodo<br>Standard<br>Daylight|
||||||||
|bySetPosition | Int\[] | RecurrenceRule | 4.3.3 | rrule/exrule \[ BYSETPOS ]||Vevent<br>Vtodo<br>Standard<br>Daylight|
||||||||
|byWeekNo | Int\[] | RecurrenceRule | 4.3.3 | rrule/exrule \[ BYWEEKNO ]||Vevent<br>Vtodo<br>Standard<br>Daylight|
||||||||||
|byYearDay | Int\[] | RecurrenceRule | 4.3.3 | rrule/exrule \[ BYYEARDAY ]||Vevent<br>Vtodo<br>Standard<br>Daylight|
||||||||
|categories | String\[Boolean] | Event|4.2.10 |categories|Vevent|
|categories | String\[Boolean] | Task|4.2.10 |categories|Vtodo|
|categories | String\[Boolean] | Group | 4.2.10 | |||
||||||||
|cid | String | Link | 1.4.11 | | |(url/X-URL) X-CID|
||||||||
|color | String | Event|4.2.11 | color|Vevent|
|color | String | Task|4.2.11 | color|Vtodo|
|color | String | Group | 4.2.11 | color||Vcalendar|
||||||||
|comments | String\[] | TimeZoneRule | 4.7.2 | comment||Standard<br>Daylight"|
||||||||
|contentType | String | Link | 1.4.11 | url X-URL-<num>|Vcalendar<br>Vevent<br>Vtodo|X-CONTENTTYPE|
||||||||
|coordinates | String | Location | 4.2.5 | geo / url||Vlocation|
||||||||
|count | UnsignedInt | RecurrenceRule | 4.3.3 | rrule/exrule \[ COUNT ]||Vevent<br>Vtodo<br>Standard<br>Daylight"|||
||||||||
|created | UTCDateTime | Event|4.1.5 | created | Vevent|
|created | UTCDateTime | Task|4.1.5 | created | Vevent|
|created | UTCDateTime | Group | 4.1.5 | created |Vcalendar|
||||||||
|day | String | NDay | 4.3.3 | rrule/exrule \[ BYDAY ]||Vevent<br>Vtodo<br>Standard<br>Daylight|
||||||||
|daylight | TimeZoneRule\[] | TimeZone | 4.7.2 |  | Daylight|
||||||||
|delegatedFrom | Id\[Boolean] | Participant | 4.4.6 |  |  | Attendee:DELEGATEDFROM|
||||||||
|delegatedTo | Id\[Boolean] | Participant | 4.4.6 |  |  | Attendee:DELEGATEDTO|
||||||||
|description | String | Event | 4.2.2, | description | Vevent|
|description | String | Task | 4.2.2, | description | Vtodo|
|description | String | Location, | 4.2.5, | description | Vlocation|
|description | String | Participant, | 4.4.6, | description | Particpant|
|description | String | VirtualLocation | 4.2.6 | ||||
||||||||
|descriptionContentType|String | Event|4.2.3 | description | Vevent|X-descriptionContentType|
|descriptionContentType|String | Task|4.2.3 | description | Vtodo|X-descriptionContentType|
||||||||
|display | String | Link | 1.4.11 | url X-URL-<num>|Vcalendar<br>Vevent<br>Vtodo"|X-DISPLAY||
||||||||
|due | LocalDateTime | Task | 5.2.1 | due||Vtodo|
||||||||
|duration | Duration | Event | 5.1.2 | duration|Vevent|if start exists (computed) dtend|
||||||||
|email | String | Participant | 4.4.6 | calendaraddress||Particpant|
||||||||
|entries | Task/Event\[]|Group|5.3.1 | |||
||||||||
|estimatedDuration | Duration | Task | 5.2.3 | duration|Vtodo|if due exists due param X-ESTIMATEDDURATION|
||||||||
|excluded | Boolean | Event|4.3.6 | X-EXCLUDED||Vevent|
|excluded | Boolean | Task|4.3.6 | X-EXCLUDED||Vtodo|
||||||||
|excludedRecurrenceRules|RecurrenceRule\[] | Event|4.3.4 | exrule||Vevent|
|excludedRecurrenceRules|RecurrenceRule\[] | Task|4.3.4 | exrule||Vtodo|||
||||||||
|expectReply | Boolean | Participant | 4.4.6 |  |  | Attendee:RSVP||
||||||||
|features | String\[Boolean] | VirtualLocation | 4.2.6 | X-\<feature>||Vlocation|||
||||||||
|firstDayOfWeek | String | RecurrenceRule | 4.3.3 | rrule/exrule \[ WKST ]||Vevent<br>Vtodo<br>Standard<br>Daylight"|||
||||||||
||||||||
|freeBusyStatus | String | Event|4.4.2 | X-FREEBUSYSTATUS||Vevent|
|freeBusyStatus | String | Task|4.4.2 | X-FREEBUSYSTATUS||Vtodo|
||||||||
|frequency | String | RecurrenceRule | 4.3.3 | rrule/exrule \[ FREQ ]||Vevent<br>Vtodo<br>Standard<br>Daylight"|
||||||||
|href | String | Link | 1.4.11 | url X-URL-<num>||Vcalendar<br>Vevent<br>Vtodo"|value|
||||||||
|interval | UnsignedInt | RecurrenceRule | 4.3.3 | rrule/exrule \[ INTERVAL ]|Vevent<br>Vtodo<br>Standard<br>Daylight"|
||||||||
|invitedBy | Id | Participant | 4.4.6 | X-INVITEDBY||Particpant<br>>Attendee:X-INVITEDBY|
||||||||
|keywords | String\[Boolean] | Event|4.2.9 | X-KEYWORDS||Vevent|comma separated list|
|keywords | String\[Boolean] | Task|4.2.9 | X-KEYWORDS||Vtodo|comma separated list|
|keywords | String\[Boolean] | Group | 4.2.9 | X-KEYWORDS|Vcalendar|comma separated list|
||||||||
|kind | String | Participant | 4.4.6 | ||Attendee:CUTYPE|
||||||||
|language | String | Participant | 4.4.6 | X-LANGAGE|Particpant|Attendee:LANGUAGE||
||||||||
|links | Id\[Link] | Group|4.2.7, | Image, url||Vcalendar|
|links | Id\[Link] | Event|4.2.7, | Image, structured_data, url||Vevent|
|links | Id\[Link] | Task|4.2.7, | Image, structured_data, url||Vtodo|
|links | Id\[Link] | Location|4.2.5, | structured_data||Vlocation|
|links | Id\[Link] | Participant | 4.4.6 | structured_data url||Particpant|Attendee:X-HREF
||||||||
|locale | String | Group|4.2.8 | ||||
|locale | String | Event|4.2.8 |  | Vevent|LANGUAGE|
|locale | String | Task | 4.2.8 | Vtodo|LANGUAGE||
||||||||
|localizations | String\[PatchObject]|Event|4.6.1 | X-LOCALIZATIONS-<num>||Vevent|||
|localizations | String\[PatchObject]|Task|4.6.1 | X-LOCALIZATIONS-<num>||Vtodo|||
||||||||
|locationId | Id | Participant | 4.4.6 |  | Participant|Vlocation||pointer|
||||||||
|locations | Id\[Location] | Event|4.2.5 |  | Vevent/Vlocations|
|locations | Id\[Location] | Task|4.2.5 |  | Vtodo/Vlocations|
||||||||
|locationTypes | String\[Boolean] | Location | 4.2.5 | loctiontype||Vlocation||comma separated list|
||||||||
||||||||
|memberOf | Id\[Boolean] | Participant | 4.4.6 |  |  | Attendee:MEMBER|
||||||||
|method | String | Event|4.1.8 | mehod|Vcalendar|
|method | String | Task|4.1.8 | mehod|Vcalendar|
||||||||
|name | String | Location, | 4.2.5, | name|Vlocation|LANGUAGE may have locale
|   |  | ||||||||
|name | String | VirtualLocation,|4.2.6, | name|Vlocation|LANGUAGE may have locale
||||||||
|name | String | Participant | 4.4.6 | summary|Particpant|
||||||||
|names | String\[Boolean] | TimeZoneRule | 4.7.2 | tzname|Standard<br>Daylight|
||||||||
|nthOfPeriod | Int | NDay | 4.3.3 | rrule/exrule \[ BYDAY ]||Vevent<br>Vtodo<br>Standard<br>Daylight|
||||||||
|offset | SignedDuration | OffsetTrigger | 4.5.2 | trigger|Valarm|
||||||||||
|offsetFrom | UTCDateTime | TimeZoneRule | 4.7.2 | offsetfrom|Standard<br>Daylight|
||||||||
|offsetTo | UTCDateTime | TimeZoneRule | 4.7.2 | offsetto|Standard<br>Daylight|
||||||||
|participants | Id\[Participant] | Event|4.4.6 |  | Vevent|Particpant|
||||||Attendee|Vevent|
|participants | Id\[Participant] | Task|4.4.6 |  | Vtodo|Particpant|
||||||Attendee|Vtodo|
||||||||
|participationComment|String | Participant | 4.4.6 | comment|Particpant|Attendee:X-COMMENT
||||||||
|participationStatus | String | Participant | 4.4.6 | status | Particpant|Attendee:PARTSTAT||
||||||||
|percentComplete | UnsignedInt | Task, | 5.2.4 | percentComplete | Vtodo|
|percentComplete | UnsignedInt | Participant | 5.2.4 | X-PERCENTCOMPLETE|Particpant|Attendee:X-PERCENTCOMPLETE
||||||||
|priority | Int | Event|4.4.1 | priority | Vevent|
|priority | Int | Task | 4.4.1 | priority | Vtodo|
||||||||
|privacy | String | Event|4.4.3 | class|Vevent|
|privacy | String | Task|4.4.3 | class|Vtodo|
||||||||
|progress | String | Task, | 5.2.5 | status|Vtodo|||
|progress | String | Participant | 5.2.5 | X-PROGRESS|Particpant|Attendee:X-PROGRESS
||||||||
|progressUpdated | UTCDateTime | Task, | 5.2.6 | lastmodified|Vtodo|
|progressUpdated | UTCDateTime | Participant | 5.2.6 | X-PROGRESSUPDATED|Particpant|Attendee:X-PROGRESSUPDATED
||||||||
|recurrenceId | LocalDateTime | Event|4.3.1 | recurrenceId | Vevent|
|recurrenceId | LocalDateTime | Task|4.3.1 | recurrenceId | Vtodo|
||||||||
|recurrenceIdTimeZone|TimeZoneId|Event|4.3.2 | recurrenceId | Vevent|
|recurrenceIdTimeZone|TimeZoneId|Task|4.3.2 | recurrenceId | Vtodo|
||||||||
|recurrenceOverrides | LocalDateTime\[PatchObject]|Event|4.3.5, | rdate|Vevent|PatchObject keys as x-?||
|recurrenceOverrides | LocalDateTime\[PatchObject]|Task|4.3.5, | rdate|Vtodo|PatchObject keys as x-?||
|recurrenceOverrides | LocalDateTime\[PatchObject]|TimeZoneRule | 4.7.2 | rdate|Standard<br>DayLight|PatchObject keys as x-?
||||||||
|recurrenceRules | RecurrenceRule\[] | Event|4.3.3, | rrule|Vevent|
|recurrenceRules | RecurrenceRule\[] | Task|4.3.3, | rrule|Vtodo|
|recurrenceRules | RecurrenceRule\[] | TimeZoneRule | 4.7.2 | rrule||Standard<br>DayLight|
||||||||
|rel | String | Link | 1.4.11 | url X-URL-<num>|Vcalendar<br>Vevent<br>Vtodo|X-REL|
||||||||
|relatedTo | String\[Relation] | Event|4.1.3, | relatedto|Vevent|RELTYPE/x-?||
|relatedTo | String\[Relation] | Task|4.1.3, | relatedto|Vtodo|RELTYPE/x-?||
|relatedTo | String\[Relation] | Alert | 4.5.2 | relatedto|Valarm|RELTYPE/x-?||
||||||||
|relation | String\[Boolean] | Relation | 1.4.10 | |Vevent<br>Vtodo<br>Valarm|relatedto::RELATED/X-?
||||||||
|relativeTo | String | OffsetTrigger, | 4.5.2, | ||||||
|relativeTo | String | Location | 4.2.5 | X-RELATIVETO|Vlocation|||
||||||||
|replyTo | String\[String] | Event|4.4.4 | organizer|X-REPYTO-<num>|Vevent|||
|replyTo | String\[String] | Task|4.4.4 | organizer|X-REPYTO-<num>|Vtodo|||
||||||||
|requestStatus | String | Event|4.4.7 | requeststatus|Vevent|
|requestStatus | String | Task|4.4.7 | requeststatus|Vtodo|
||||||||
|roles | String\[Boolean] | Participant | 4.4.6 | participanttype|Particpant|Attendee:ROLE<br>>Attendee:X-PARTICPANTTYE|Comma separated list|
||||||||
|rscale | String | RecurrenceRule | 4.3.3 | rrule/exrule \[ RSCALE ]|Vevent<br>Vtodo<br>Standard<br>Daylight"|
||||||||
|sentBy | String | Event|4.4.5, | X-SENTBY|Vevent|
|sentBy | String | Task|4.4.5, | X-SENTBY|Vtodo|
|sentBy | String | Participant | 4.4.6 | ||Attendee:X-SENT-BY
||||||||
|standard | TimeZoneRule\[] | TimeZone | 4.7.2 | ||Standard|
||||||||
|start | LocalDateTime | TimeZoneRule | 4.7.2 | dtstart|Standard<br>Daylight|
||||||||
|scheduleAgent | String | Participant | 4.4.6 | X-SCHEDULEAGENT|Particpant|
||||||||
|scheduleForceSend | Boolean | Participant | 4.4.6 | X-SCHEDULEFORCESEND|Particpant|||
||||||||
|scheduleSequence | UnsignedInt | Participant | 4.4.6 | X-SCHEDULESEQUENCE|Particpant|||
||||||||
|scheduleStatus | String\[] | Participant | 4.4.6 | requeststatus|Particpant|Attendee:X-SCHEDULESTATUS
||||||||
|scheduleUpdated | UTCDateTime | Participant | 4.4.6 | X-SCHEDULEUPDATED|Particpant|Attendee:X-SCHEDULEUPDATED
||||||||
|sendTo | String\[String] | Participant | 4.4.6 | Contact|Particpant|
||||||||
|sequence | UnsignedInt | Event|4.1.7 | sequence|Vevent|
|sequence | UnsignedInt | Task|4.1.7 | sequence|Vtodo|
||||||||
|showWithoutTime | Boolean | Event|4.2.4 | ||dtstart/dtend: VALUE = DATE (if true)|
|showWithoutTime | Boolean | Task|4.2.4 | ||dtstart/dtend: VALUE = DATE (if true)|
||||||||
|size | UnsignedInt | Link | 1.4.11 | url X-URL-\<num>|Vcalendar<br>Vevent<br>Vtodo|X-SIZE
||||||||
|skip | String | RecurrenceRule | 4.3.3 | rrule/exrule \[ SKIP ]|Vevent<br>Vtodo<br>Standard<br>Daylight|
||||||||
|source | String | Group | 5.3.2 | source|Vcalendar|
||||||||
|start | LocalDateTime | Event|5.1.1, | dtstart|Vevent|
|start | LocalDateTime | Task|5.2.2 | dtstart|Vtodo|
||||||||
|status | String | Event | 5.1.3 | status | Vevent|
||||||||
|timeZone | TimeZoneId|Event|4.7.1, | ||Vtimezone::tzid|
|timeZone | TimeZoneId|Task|4.7.1, | ||Vtimezone::tzid|
|timeZone | TimeZoneId|Location | 4.2.5 | X-TIMEZONE|Vlocation|Vtimezone::tzid|
||||||||
|timeZones | TimeZoneId\[TimeZone]|Event|4.7.2 | ||Vtimezone|
|timeZones | TimeZoneId\[TimeZone]|Task|4.7.2 | ||Vtimezone|
||||||||
|title | String | Event|4.2.1 | name|Vevent|
|title | String | Task|4.2.1 | summary|Vtodo|
|title | String | Group|4.2.1 | name|Vcalendar|
|title | String | Link|4.2.1 | url X-URL-<num>|Vcalendar<br>Vevent<br>Vtodo|X-TITLE||
||||||||
|trigger | OffsetTrigger<br>AbsoluteTrigger<br>UnknownTrigger|Alert | 4.5.2 | trigger|Valarm|
||||||||
|tzId | String | TimeZone | 4.7.2 | tzid||Vtimezone|
||||||||||
|uid | String | Event|4.1.2 | uid|Vevent|
|uid | String | Task|4.1.2 | uid|Vtodo|
|uid | String | Group | 4.1.2 | uid|Vcalendar|
||||||||
|until | LocalDateTime | RecurrenceRule | 4.3.3 | rrule/exrule \[ UNTIL ]|Vevent<br>Vtodo<br>Standard<br>Daylight|||
||||||||
|updated | UTCDateTime | Event|4.1.6 | lastmodified|Vevent|
|updated | UTCDateTime | Task|4.1.6 | lastmodified|Vtodo|
|updated | UTCDateTime | Group | 4.1.6 | lastmodified|Vcalendar|
|updated | UTCDateTime | TimeZone | 4.7.2 | lastmodified|Vtimezone|
||||||||
|uri | String | VirtualLocation | 4.2.6 | url|Vlocation|
||||||||
|url | String | TimeZone | 4.7.2 | tzurl|Vtimezone|
||||||||
|useDefaultAlerts | Boolean | Event|4.5.1 | X-USEDEFAULTALERTS|Vevent|'TRUE’/’FALSE’|
|useDefaultAlerts | Boolean | Task|4.5.1 | X-USEDEFAULTALERTS|Vtodo|'TRUE’/’FALSE’|
||||||||
|validUntil | UTCDateTime | TimeZone | 4.7.2 | tzuntil|Vtimezone|
||||||||
|virtualLocations|Id\[VirtualLocation]|Event|4.2.6 | |Vevent/Vlocation|X-prop<br>X-VIRTUALLOCATION = 1|
|virtualLocations|Id\[VirtualLocation]|Task|4.2.6 | |Vtodo/Vlocation|X-prop<br>X-VIRTUALLOCATION = 1|
||||||||
|when | UTCDateTime | AbsoluteTrigger | 4.5.2 | trigger|Valarm|
||||||||
