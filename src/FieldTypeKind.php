<?php

namespace SharePoint\PHP\Client;

/**
 * Specifies the type of the field.
 * 
 * @link https://msdn.microsoft.com/en-us/library/microsoft.sharepoint.client.fieldtype.aspx
 * @author usruoc
 *        
 */
abstract class FieldTypeKind extends Enum {
	/**
	 * Must not be used.
	 * @var int
	 */
	const Invalid = 0;
	/**
	 * Specifies that the field contains an integer value.
	 * Field allows an integer value.
	 * @var int
	 */
	const Integer = 1;
	/**
	 * Specifies that the field contains a single line of text.
	 * Field allows a limited-length string of text.
	 * @var int
	 */
	const Text = 2;
	/**
	 * Specifies that the field contains multiple lines of text.
	 * Field allows larger amounts of text.
	 * @var intF
	 */
	const Note = 3;
	/**
	 * Specifies that the field contains a date and time value or a date-only value.
	 * Field allows full date and time values, as well as date-only values
	 * @var int
	 */
	const DateTime = 4;
	/**
	 * Specifies that the field contains a monotonically increasing integer.
	 * Counter is a monotonically increasing integer field, and has a unique value in relation to other values that are stored for the field in the list. Counter is used only for the list item identifier field, and not intended for use elsewhere.
	 * @var int
	 */
	const Counter = 5;
	/**
	 * Specifies that the field contains a single value from a set of specified values.
	 * Field allows selection from a set of suggested values. A choice field supports a field-level setting which specifies whether free-form values are supported.
	 * @var int
	 */
	const Choice = 6;
	/**
	 * Specifies that the field is a lookup field.
	 * Field allows a reference to another list item. The field supports specification of a list identifier for a targeted list. An optional site identifier can also be specified, which specifies the site of the list which contains the target of the lookup.
	 * @var int
	 */
	const Lookup = 7;
	/**
	 * Specifies that the field contains a Boolean value.
	 * Field allows a true or false value.
	 * @var int
	 */
	const Boolean = 8;
	/**
	 * Specifies that the field contains a number value.
	 * Field allows a positive or negative number. A number field supports a field level setting used to specify the number of decimal places to display.
	 * @var int
	 */
	const Number = 9;
	/**
	 * Specifies that the field contains a currency value.
	 * Field allows for currency-related data. The Currency field has a CurrencyLocaleId property which takes a locale identifier of the currency to use.
	 * @var int
	 */
	const Currency = 10;
	/**
	 * Specifies that the field contains a URI and an optional description of the URI
	 * 
	 * @var int
	 */
	const URL = 11;
	/**
	 * Specifies that the field is a computed field.
	 * Field renders output based on the value of other columns.
	 * @var int
	 */
	const Computed = 12;
	/**
	 * Specifies that the field indicates the thread for a discussion item in a threaded view of a discussion board.
	 * Contains data on the threading of items in a discussion board.
	 * @var int
	 */
	const Threading = 13;
	/**
	 * Specifies that the field contains a GUID value.
	 * Specifies that the value of the field is a GUID.
	 * @var int
	 */
	const Guid = 14;
	/**
	 * Specifies that the field contains one or more values from a set of specified values
	 * Field allows one or more values from a set of specified choices. A MultiChoice field can also support free-form values.
	 * @var int
	 */
	const MultiChoice = 15;
	/**
	 * Specifies that the field contains rating scale values for a survey list.
	 * Grid choice supports specification of multiple number scales in a list.
	 * @var int
	 */
	const GridChoice = 16;
	/**
	 * Specifies that the field is a calculated field.
	 * Field value is calculated based on the value of other columns.
	 * @var int
	 */
	const Calculated = 17;
	/**
	 * Specifies that the field contains the leaf name of a document as a value.
	 * Specifies a reference to a file that can be used to retrieve the contents of that file.
	 * @var int
	 */
	const File = 18;
	/**
	 * Specifies that the field indicates whether the list item has attachments.
	 * Field describes whether one or more files are associated with the item. See Attachments for more information on attachments. true if a list item has attachments, and false if a list item does not have attachments. 
	 * @var int
	 */
	const Attachments = 19;
	/**
	 * Specifies that the field contains one or more users and groups as values.
	 * A lookup to a particular user in the User Info list.
	 * @var int
	 */
	const User = 20;
	/**
	 * Specifies that the field indicates whether a meeting in a calendar list recurs.
	 * Specifies whether a field contains a recurrence pattern for an item.
	 * @var int
	 */
	const Recurrence = 21;
	/**
	 * Specifies that the field contains a link between projects in a Meeting Workspace site.
	 * Field allows a link to a Meeting Workspace site.
	 * @var int
	 */
	const CrossProjectLink = 22;
	/**
	 * Specifies that the field indicates moderation status.
	 * Specifies the current status of a moderation process on the document. Value corresponds to one of the moderation status values.
	 * @var int
	 */
	const ModStat = 23;
	/**
	 * Specifies that the type of the field was set to an invalid value.
	 * Specifies errors.
	 * @var int
	 */
	const Error = 24;
	/**
	 * Specifies that the field contains a content type identifier as a value.
	 * Field contains a content type identifier for an item. ContentTypeId conforms to the structure defined in ContentTypeId.
	 * @var int
	 */
	const ContentTypeId = 25;
	/**
	 * Specifies that the field separates questions in a survey list onto multiple pages.
	 * Represents a placeholder for a page separator in a survey list. PageSeparator is only intended to be used with a Survey list.
	 * @var int
	 */
	const PageSeparator = 26;
	/**
	 * Specifies that the field indicates the position of a discussion item in a threaded view of a discussion board.
	 * Contains a compiled index of threads in a discussion board.
	 * @var int
	 */
	const ThreadIndex = 27;
	/**
	 * Specifies that the field indicates the status of a workflow instance on a list item.
	 * Contains status on a running workflow for a particular item.
	 * @var int
	 */
	const WorkflowStatus = 28;
	/**
	 * Specifies that the field indicates whether a meeting in a calendar list is an all-day event.
	 * The AllDayEvent field is only used in conjunction with an Events list. true if the item is an all day event (that is, does not occur during a specific set of hours in a day).
	 * @var int
	 */
	const AllDayEvent = 29;
	/**
	 * Specifies that the field contains the most recent event in a workflow instance.
	 * A description of a type of a historical workflow event. See WorkflowEventType Enumeration for more information.
	 * @var int
	 */
	const WorkflowEventType = 30;
	/**
	 * Must not be used.
	 * Specifies the maximum number of items. 
	 * @var int
	 */
	const MaxItems = 31;
}