# Assessment Editor Drag & Drop Behavior

## Overview
The assessment editor uses UIKit's nestable component for drag-and-drop functionality to reorder questions and dimension descriptions.

## Key Behavior

### ✅ **Draggable Elements**
- **Questions with visible numbers** (types 1, 3, 4, 5, 6, 7, 8, 9, 10)
- These elements have a visible question number and can be dragged to reorder

### ❌ **Non-Draggable Elements** 
- **Dimension descriptions** (type 2)
- These elements have hidden question numbers (`style="display:none;"`) and cannot be dragged

## Technical Implementation

### HTML Structure
```html
<!-- Question Number (hidden for type 2) -->
<div id="number"<?php if ($type == 2) echo ' style="display:none;"'; ?>>{{ $number }}</div>

<!-- Drag Handle (always present) -->
<div class="reorder uk-nestable-handle"></div>
```

### CSS Classes
- `.uk-nestable` - Container for draggable list
- `.uk-nestable-handle` - Drag handle (always present)
- `.uk-nestable-list-item` - Individual draggable items

### JavaScript Behavior
```javascript
// Updates question numbers after drag operation
$(".questions").on('nestable-stop', function(ev) {
    update_question_numbers();
});

// Numbers all question types, including descriptions
function update_question_numbers() {
    var number = 1;
    $('.questions .question').each(function() {
        $('#number', this).html(number);
        number += 1;
    });
}
```

## Why This Design?

### **Dimension Descriptions (Type 2)**
- **Purpose**: Provide context and instructions for dimension sections
- **Behavior**: Fixed position relative to their associated questions
- **Rationale**: Prevents accidental reordering that would break assessment flow

### **Questions (Types 1, 3-10)**
- **Purpose**: Actual assessment questions that users answer
- **Behavior**: Fully draggable and reorderable
- **Rationale**: Allows flexible question ordering within dimensions

## User Experience

### ✅ **What You Can Do**
- Reorder questions within the same dimension
- Move questions between different dimensions
- Add new questions anywhere in the list
- Delete questions

### ❌ **What You Cannot Do**
- Drag dimension descriptions above/below questions
- Reorder dimension descriptions relative to each other
- Move dimension descriptions to different positions

## Technical Notes

### **Question Types**
- **Type 1**: Multiple Choice
- **Type 2**: Description (non-draggable)
- **Type 3**: Text Input
- **Type 4**: Rating Scale
- **Type 5**: Yes/No
- **Types 6-10**: React Components (various)

### **Database Storage**
- All elements (including descriptions) are stored as questions in the database
- The `number` field determines display order
- Type 2 questions have `dimension_id = 0` (not associated with specific dimensions)

### **Form Submission**
- All questions (including descriptions) are collected and saved
- Order is preserved based on the `number` field
- Descriptions maintain their position relative to associated questions

## Conclusion

This design ensures that:
1. **Assessment structure remains logical** - descriptions stay with their dimensions
2. **Questions remain flexible** - can be reordered as needed
3. **User experience is intuitive** - only meaningful elements are draggable

The non-draggable behavior for dimension descriptions is **intentional design**, not a bug.
