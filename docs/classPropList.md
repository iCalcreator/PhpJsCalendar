
#### PhpJsCalendar Dto class and property structure

All properties has `get`,`set` and `is\<prop\>Set` methods,<br>
for 'array' properties `get`, `get\<Prop\>Count`, `add` and `set`methods.

| Class | Property Name | Property Type|
| --- | --- | --- |
||||
| **AbsoluteTrigger** | when | **DateTime**(UTC) |
||||
| **Alert** | acknowledged | **DateTime**(UTC) |
| | action | string|
| | relatedTo | string\[**Relation**] |
| | trigger | **OffsetTrigger**/**AbsoluteTrigger**/**UnknownTrigger** |
||||
| **Event** | mediaType | string<br>static<br>'application/jscalendar+json;type=event'|
| | alerts | id\[**Alert**]|
| | categories | string\[boolean] |
| | color | string |
| | created | **DateTime**(UTC) |
| | description | string |
| | descriptionContentType | string |
| | excluded | boolean |
| | excludedRecurrenceRules | **RecurrenceRule**\[] |
| | freeBusyStatus | string |
| | keywords | string\[boolean] |
| | links | id\[**Link**] |
| | locale | string |
| | localizations | string\[**PatchObject**] |
| | locations | id\[**Location**] |
| | method | string |
| | participants | id\[**Participant**] |
| | priority | int |
| | privacy | string |
| | recurrenceId | **DateTime**(local) |
| | recurrenceIdTimeZone | TimeZoneId |
| | recurrenceOverrides | **DateTime**(local)\[**PatchObject**] |
| | recurrenceRules | **RecurrenceRule**\[] |
| | relatedTo | string\[**Relation**] |
| | replyTo | string\[string] |
| | requestStatus | string |
| | sentBy | string |
| | sequence | unsignedInt |
| | showWithoutTime | boolean |
| | start | **DateTime**(local) |
| | timeZone | TimeZoneId |
| | timeZones | TimeZoneId\[**TimeZone**] |
| | title | string |
| | uid | string<br>default random guid |
| | updated | **DateTime**(UTC) |
| | useDefaultAlerts | boolean |
| | virtualLocations | id\[**VirtualLocation**] |
| | duration | **Duration** |
| | status | string |
||||
| **Group** | mediaType | string<br>static<br>'application/jscalendar+json;type=group'|
| | entries | (**Task**/**Event**)\[] |
| | links | id\[**Link**] |
| | locale | string |
| | title | string |
| | categories | string\[boolean] |
| | color | string |
| | created | **DateTime**(UTC) |
| | keywords | string\[boolean] |
| | source | string |
| | uid | string<br>default random guid |
| | updated | **DateTime**(UTC) |
||||
| **Link** | title | string |
| | cid | string<br>default random guid |
| | contentType | string |
| | display | string |
| | href | string |
| | rel | string |
| | size | unsignedInt |
||||
| **Location** | links | id\[**Link**] |
| | coordinates | string |
| | description | string |
| | locationTypes | string\[boolean] |
| | name | string |
| | relativeTo | string |
| | timeZone | TimeZoneId |
||||
| **NDay** | day | string |
| | nthOfPeriod | int |
||||
| **OffsetTrigger** | offset | **Duration**(signed) |
| | relativeTo | string |
||||
| **Participant** | delegatedFrom | id\[boolean]|
| | delegatedTo | id\[boolean] |
| | description | string |
| | email | string |
| | expectReply | boolean |
| | invitedBy | id |
| | kind | string |
| | language | string |
| | links | id\[**Link**] |
| | locationId | id |
| | memberOf | id\[boolean] |
| | name | string |
| | participationComment | string |
| | participationStatus | string |
| | percentComplete | unsignedInt |
| | progress | string |
| | progressUpdated | **DateTime**(UTC) |
| | roles | string\[boolean] |
| | sentBy | string |
| | scheduleAgent | string |
| | scheduleForceSend | boolean |
| | scheduleSequence | unsignedInt |
| | scheduleStatus | string\[] |
| | scheduleUpdated | **DateTime**(UTC) |
| | sendTo | string\[string] |
||||
| **RecurrenceRule** | byDay | **NDay**\[] |
| | byHour | unsignedInt\[] |
| | byMinute | unsignedInt\[] |
| | byMonth | string\[] |
| | byMonthDay | int\[] |
| | bySecond | unsignedInt\[] |
| | bySetPosition | int\[] |
| | byWeekNo | int\[] |
| | byYearDay | int\[] |
| | count | unsignedInt |
| | firstDayOfWeek | string |
| | frequency | string |
| | interval | unsignedInt |
| | rscale | string |
| | skip | string |
| | until | **DateTime**(local) |
||||
| **Relation** | relation | string\[boolean] |
||||
| **Task** | mediaType | string<br>static<br>'application/jscalendar+json;type=task'|
| | alerts | id\[**Alert**] |
| | categories | string\[boolean] |
| | color | string |
| | created | **DateTime**(UTC) |
| | description | string |
| | descriptionContentType | string |
| | due | **DateTime**(local) |
| | estimatedDuration | **Duration** |
| | excluded | boolean |
| | excludedRecurrenceRules | **RecurrenceRule**\[] |
| | freeBusyStatus | string |
| | keywords | string\[boolean] |
| | links | id\[**Link**] |
| | locale | string |
| | localizations | string\[**PatchObject**] |
| | locations | id\[**Location**] |
| | method | string |
| | participants | id\[**Participant**] |
| | percentComplete | unsignedInt |
| | priority | Int |
| | privacy | string |
| | progress | string |
| | progressUpdated | **DateTime**(UTC) |
| | recurrenceId | **DateTime**(local) |
| | recurrenceIdTimeZone | TimeZoneId |
| | recurrenceOverrides | **DateTime**(local)\[**PatchObject**] |
| | recurrenceRules | **RecurrenceRule**\[] |
| | relatedTo | string\[**Relation**] |
| | replyTo | string\[string] |
| | requestStatus | string |
| | sentBy | string |
| | sequence | unsignedInt |
| | showWithoutTime | boolean |
| | start | **DateTime**(local) |
| | timeZone | TimeZoneId |
| | timeZones | TimeZoneId\[**TimeZone**] |
| | title | string |
| | uid | string<br>default random guid |
| | updated | **DateTime**(UTC) |
| | useDefaultAlerts | boolean |
| | virtualLocations | id\[**VirtualLocation**] |
||||
| **TimeZone** | aliases | string\[boolean] |
| | daylight | **TimeZoneRule**\[] |
| | standard | **TimeZoneRule**\[] |
| | tzId | string |
| | updated | **DateTime**(UTC) |
| | url | string |
| | validUntil | **DateTime**(UTC) |
||||
| **TimeZoneRule** | comments | string\[] |
| | names | string\[boolean] |
| | offsetFrom | **DateTime**(UTC) |
| | offsetTo | **DateTime**(UTC) |
| | recurrenceOverrides | **DateTime**(local)\[**PatchObject**] |
| | recurrenceRules | **RecurrenceRule**\[] |
| | start | **DateTime**(local) |
||||
| **VirtualLocation** | description | string |
| | features | string\[boolean] |
| | name | string |
| | uri | string |
