# Cross-References to Other Codebases

**Purpose:** Links to related documentation and work in other codebases within the Astacala Rescue workspace.

## Related Documentation

### Mobile App References
- Link to mobile app documentation when web app changes affect mobile integration
- Authentication system coordination  
- Data synchronization considerations
- API usage coordination

### Backend References
- Link to backend documentation when web app changes require backend modifications
- Database schema coordination
- API endpoint usage and requirements
- Authentication integration

### Integration Coordination
- Links to workspace-level integration documentation
- Cross-platform feature coordination
- Strategic decisions affecting multiple codebases

## How to Use This Folder

When working on web app features that affect other codebases:

1. **Create primary documentation** in the appropriate web app documentation folder
2. **Create cross-reference files** in this folder linking to the primary documentation  
3. **Include impact summary** specific to how the web changes affect other codebases
4. **Link to integration coordination** if workspace-level coordination is needed

## Cross-Reference File Format

```markdown
# [Feature Name] - Web App Cross-Reference

**Primary Documentation:** [Link to main documentation in web app]
**Related Work:** [Links to related work in other codebases]
**Integration Impact:** [Summary of how this affects other platforms]

## Web App Changes Summary
[Brief summary of web app changes]

## Impact on Mobile App
[How these changes affect mobile functionality]

## Impact on Backend
[How these changes affect backend requirements]

## Action Items for Other Codebases
- [ ] Mobile: [Specific actions needed]
- [ ] Backend: [Specific actions needed]
```

---

**For complete multi-codebase documentation guidelines, see:**
`../../../../astacala_rescue_mobile/documentation/AI_AGENT_DOCUMENTATION_SYSTEM_V2.md`
